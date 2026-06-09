<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ServiceInquiryResultEmail;
use App\Models\Client;
use App\Models\PermitApplication;
use App\Models\ServiceInquiry;
use App\Notifications\ClientWelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ServiceInquiryController extends Controller
{
    /**
     * Display a listing of service inquiries
     */
    public function index(Request $request)
    {
        $query = ServiceInquiry::with(['client', 'contactedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('inquiry_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $inquiries = $query->paginate(20);

        // Stats for dashboard cards — single aggregated query instead of 8 separate counts
        $startOfWeek = now()->startOfWeek();
        $startOfMonth = now()->startOfMonth();
        $statsRow = DB::table('service_inquiries')->selectRaw("
            COUNT(*) as total,
            SUM(status = 'new') as new,
            SUM(status = 'analyzed') as analyzed,
            SUM(status = 'contacted') as contacted,
            SUM(status = 'converted') as converted,
            SUM(priority = 'high') as high_priority,
            SUM(created_at >= ?) as this_week,
            SUM(created_at >= ?) as this_month
        ", [$startOfWeek, $startOfMonth])->first();

        $stats = (array) $statsRow;

        return view('admin.service-inquiries.index', compact('inquiries', 'stats'));
    }

    /**
     * Display the specified inquiry
     */
    public function show(ServiceInquiry $serviceInquiry)
    {
        $serviceInquiry->load(['client', 'convertedToApplication', 'contactedBy', 'shapefileProject']);

        return view('admin.service-inquiries.show', compact('serviceInquiry'));
    }

    /**
     * Update inquiry status
     */
    public function updateStatus(Request $request, ServiceInquiry $serviceInquiry)
    {
        $request->validate([
            'status' => 'required|in:new,processing,analyzed,contacted,qualified,converted,registered,lost',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $serviceInquiry->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'last_contacted_at' => in_array($request->status, ['contacted', 'qualified']) ? now() : $serviceInquiry->last_contacted_at,
            'contacted_by' => in_array($request->status, ['contacted', 'qualified']) ? auth()->id() : $serviceInquiry->contacted_by,
        ]);

        return back()->with('success', 'Status berhasil diupdate');
    }

    /**
     * Update priority
     */
    public function updatePriority(Request $request, ServiceInquiry $serviceInquiry)
    {
        $request->validate([
            'priority' => 'required|in:low,medium,high',
        ]);

        $serviceInquiry->update(['priority' => $request->priority]);

        return back()->with('success', 'Prioritas berhasil diupdate');
    }

    /**
     * Add admin notes
     */
    public function addNote(Request $request, ServiceInquiry $serviceInquiry)
    {
        $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        $currentNotes = $serviceInquiry->admin_notes ?? '';
        $timestamp = now()->format('Y-m-d H:i');
        $user = auth()->user()->name;

        $newNote = "[{$timestamp}] {$user}: {$request->note}";
        $updatedNotes = $currentNotes ? $currentNotes."\n\n".$newNote : $newNote;

        $serviceInquiry->update(['admin_notes' => $updatedNotes]);

        return back()->with('success', 'Catatan berhasil ditambahkan');
    }

    /**
     * Convert inquiry to client project
     */
    public function convertToProject(Request $request, ServiceInquiry $serviceInquiry)
    {
        $request->validate([
            'create_client_account' => 'required|boolean',
            'password' => 'required_if:create_client_account,true|min:8',
        ]);

        DB::beginTransaction();
        try {
            // Check if email already exists
            $client = Client::where('email', $serviceInquiry->email)->first();

            if (! $client && $request->create_client_account) {
                // Create new client account
                $client = Client::create([
                    'name' => $serviceInquiry->contact_person,
                    'email' => $serviceInquiry->email,
                    'password' => bcrypt($request->password),
                    'company_name' => $serviceInquiry->company_name,
                    'phone' => $serviceInquiry->phone,
                    'company_type' => $serviceInquiry->company_type,
                    'email_verified_at' => now(), // Auto-verify
                ]);
            }

            if ($client) {
                // Create permit application from inquiry
                $application = PermitApplication::create([
                    'application_number' => PermitApplication::generateApplicationNumber(),
                    'client_id' => $client->id,
                    'kbli_code' => $serviceInquiry->kbli_code,
                    'business_description' => $serviceInquiry->business_activity,
                    'business_scale' => $serviceInquiry->form_data['business_scale'] ?? null,
                    'location_province' => $serviceInquiry->form_data['location_province'] ?? null,
                    'location_city' => $serviceInquiry->form_data['location_city'] ?? null,
                    'status' => 'draft',
                    'submission_date' => now(),
                    'notes' => "Converted from inquiry: {$serviceInquiry->inquiry_number}\n\n".
                              "AI Analysis:\n".json_encode($serviceInquiry->ai_analysis, JSON_PRETTY_PRINT),
                ]);

                // Update inquiry
                $serviceInquiry->update([
                    'status' => 'converted',
                    'client_id' => $client->id,
                    'converted_to_application_id' => $application->id,
                    'converted_at' => now(),
                ]);

                if ($request->create_client_account && $request->password) {
                    $client->notify(new ClientWelcomeNotification($client, $request->password));
                }

                DB::commit();

                return redirect()
                    ->route('admin.service-inquiries.show', $serviceInquiry)
                    ->with('success', 'Inquiry berhasil dikonversi ke project! Client ID: '.$client->id);
            }

            DB::rollBack();

            return back()->with('error', 'Gagal membuat client account');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Export inquiries to CSV
     */
    public function export(Request $request)
    {
        $query = ServiceInquiry::orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $inquiries = $query->get();

        $filename = 'service-inquiries-'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($inquiries) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Inquiry Number',
                'Date',
                'Company Name',
                'Contact Person',
                'Email',
                'Phone',
                'Business Activity',
                'Status',
                'Priority',
                'Estimated Value',
                'Complexity Score',
            ]);

            // Data rows
            foreach ($inquiries as $inquiry) {
                fputcsv($file, [
                    $inquiry->inquiry_number,
                    $inquiry->created_at->format('Y-m-d H:i'),
                    $inquiry->company_name,
                    $inquiry->contact_person,
                    $inquiry->email,
                    $inquiry->phone,
                    $inquiry->business_activity,
                    $inquiry->status,
                    $inquiry->priority,
                    $inquiry->estimated_value ? 'Rp '.number_format($inquiry->estimated_value) : '-',
                    $inquiry->complexity_score ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Send AI result email to the inquiry's email address
     */
    public function sendResult(ServiceInquiry $serviceInquiry)
    {
        if (! $serviceInquiry->ai_analysis) {
            return back()->with('error', 'Analisis AI belum tersedia untuk inquiry ini.');
        }

        try {
            Mail::to($serviceInquiry->email)
                ->send(new ServiceInquiryResultEmail($serviceInquiry));

            // Log as admin note
            $currentNotes = $serviceInquiry->admin_notes ?? '';
            $timestamp = now()->format('Y-m-d H:i');
            $user = auth()->user()->name;
            $newNote = "[{$timestamp}] {$user}: Email hasil analisis AI dikirim ke {$serviceInquiry->email}";
            $updatedNotes = $currentNotes ? $currentNotes."\n\n".$newNote : $newNote;
            $serviceInquiry->update(['admin_notes' => $updatedNotes]);

            return back()->with('success', 'Email hasil analisis AI berhasil dikirim ke '.$serviceInquiry->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email: '.$e->getMessage());
        }
    }

    /**
     * Delete inquiry
     */
    public function destroy(ServiceInquiry $serviceInquiry)
    {
        $serviceInquiry->delete();

        return redirect()
            ->route('admin.service-inquiries.index')
            ->with('success', 'Inquiry berhasil dihapus');
    }
}
