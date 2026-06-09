<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

// Health check — tidak perlu auth
Route::get('/health', function () {
    try { DB::connection()->getPdo(); $db = 'ok'; } catch (\Exception $e) { $db = 'error: ' . $e->getMessage(); }
    try { Redis::ping(); $redis = 'ok'; } catch (\Exception $e) { $redis = 'error: ' . $e->getMessage(); }
    $status = ($db === 'ok' && $redis === 'ok') ? 'ok' : 'degraded';
    return response()->json(['status' => $status, 'service' => env('APP_NAME'), 'database' => $db, 'redis' => $redis], $status === 'ok' ? 200 : 503);
});

// CRM - Clients & Leads (Protected)
Route::prefix('v1')->middleware('jwt.auth')->group(function () {
    Route::get('clients', [App\Http\Controllers\Api\ClientController::class, 'index']);
    Route::post('clients', [App\Http\Controllers\Api\ClientController::class, 'store']);
    Route::get('clients/{client}', [App\Http\Controllers\Api\ClientController::class, 'show']);
    Route::put('clients/{client}', [App\Http\Controllers\Api\ClientController::class, 'update']);
    Route::delete('clients/{client}', [App\Http\Controllers\Api\ClientController::class, 'destroy']);

    Route::get('leads', [App\Http\Controllers\Api\ServiceInquiryController::class, 'index']);
    Route::post('leads', [App\Http\Controllers\Api\ServiceInquiryController::class, 'store']);
    Route::get('leads/{inquiry}', [App\Http\Controllers\Api\ServiceInquiryController::class, 'show']);
    Route::put('leads/{inquiry}/status', [App\Http\Controllers\Api\ServiceInquiryController::class, 'updateStatus']);
    Route::post('leads/{inquiry}/analyze', [App\Http\Controllers\Api\ServiceInquiryController::class, 'analyze']);
});

// Public endpoints — tidak perlu auth
Route::prefix('v1')->group(function () {
    Route::get('kbli', function (\Illuminate\Http\Request $r) {
        $q = \App\Models\Kbli::select('id', 'code', 'title', 'description', 'complexity_level');
        if ($r->search) $q->where(function ($qb) use ($r) {
            $qb->whereRaw('LOWER(code) LIKE ?', ['%'.strtolower($r->search).'%'])
               ->orWhereRaw('LOWER(title) LIKE ?', ['%'.strtolower($r->search).'%']);
        });
        return response()->json($q->paginate(50));
    });
    Route::get('kbli/{code}', fn($c) => response()->json(\App\Models\Kbli::whereRaw('LOWER(code) = ?', [strtolower($c)])->firstOrFail()));
    Route::get('permit-types', fn() => response()->json(\App\Models\PermitType::paginate(30)));
    Route::get('permit-types/{id}', fn($id) => response()->json(\App\Models\PermitType::with('permitTemplates')->findOrFail($id)));
});

// Protected endpoints — perlu JWT
Route::prefix('v1')->middleware('jwt.auth')->group(function () {
    Route::get('notifications', fn() => response()->json(auth()->user()?->notifications()->paginate(20) ?? []));

    // Permit Applications — core bisnis
    Route::get('permit-applications', [\App\Http\Controllers\Api\PermitApplicationController::class, 'index']);
    Route::post('permit-applications', [\App\Http\Controllers\Api\PermitApplicationController::class, 'store']);
    Route::get('permit-applications/stats', [\App\Http\Controllers\Api\PermitApplicationController::class, 'stats']);
    Route::get('permit-applications/{id}', [\App\Http\Controllers\Api\PermitApplicationController::class, 'show']);
    Route::patch('permit-applications/{id}/status', [\App\Http\Controllers\Api\PermitApplicationController::class, 'updateStatus']);
    Route::post('permit-applications/{id}/notes', [\App\Http\Controllers\Api\PermitApplicationController::class, 'addNote']);
    Route::post('permit-applications/{id}/documents', [\App\Http\Controllers\Api\PermitApplicationController::class, 'addDocument']);
    Route::get('permit-applications/{id}/checklist', [\App\Http\Controllers\Api\PermitApplicationController::class, 'getChecklist']);

    // Compliance
    Route::post('compliance/check', [\App\Http\Controllers\Api\ComplianceController::class, 'check']);
    Route::get('compliance/regulatory-changes', [\App\Http\Controllers\Api\ComplianceController::class, 'regulatoryChanges']);

    // AI Proxy
    Route::post('ai/kbli/recommend', function (\Illuminate\Http\Request $r) {
        $resp = \Illuminate\Support\Facades\Http::timeout(30)->post(env('AI_SERVICE_URL', 'http://ms_ai_service:8000') . '/api/v1/kbli/recommend', $r->all());
        return response()->json($resp->json(), $resp->status());
    });
    Route::post('ai/consultation', function (\Illuminate\Http\Request $r) {
        $resp = \Illuminate\Support\Facades\Http::timeout(60)->post(env('AI_SERVICE_URL', 'http://ms_ai_service:8000') . '/api/v1/consultation', $r->all());
        return response()->json($resp->json(), $resp->status());
    });

    // Stats
    Route::get('stats', function () {
        return response()->json([
            'kbli_total'          => \App\Models\Kbli::count(),
            'permit_types'        => \App\Models\PermitType::count(),
            'permit_templates'    => \App\Models\PermitTemplate::count(),
            'applications_total'  => class_exists(\App\Models\PermitApplication::class) ? \App\Models\PermitApplication::count() : 0,
            'applications_active' => class_exists(\App\Models\PermitApplication::class) ? \App\Models\PermitApplication::whereIn('status', ['submitted', 'under_review', 'in_progress'])->count() : 0,
        ]);
    });
});
