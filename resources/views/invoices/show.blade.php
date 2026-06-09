@extends('layouts.admin')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div class="flex items-center">
            <a href="{{ route('projects.show', ['project' => $invoice->project_id, 'tab' => 'financial']) }}" 
               class="text-apple-blue-dark hover:text-apple-blue mr-4">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold font-mono" style="color: #FFFFFF;">{{ $invoice->invoice_number }}</h1>
                <p class="mt-1" style="color: rgba(235, 235, 245, 0.6);">Invoice Details</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('exports.invoice-detail', $invoice) }}" 
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('invoices.download-pdf', $invoice) }}" target="_blank"
               class="px-4 py-2 rounded-lg font-medium transition-colors" 
               style="background: rgba(255, 149, 0, 0.9); color: #FFFFFF;">
                <i class="fas fa-file-pdf mr-2"></i>Download PDF
            </a>
            @if($invoice->status === 'draft')
            <button onclick="sendInvoice()" 
                    class="px-4 py-2 rounded-lg font-medium transition-colors" 
                    style="background: rgba(0, 122, 255, 0.9); color: #FFFFFF;">
                <i class="fas fa-paper-plane mr-2"></i>Send Invoice
            </button>
            @endif
        </div>
    </div>

    <!-- Invoice Status & Info -->
    <div class="card-elevated rounded-apple-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Status</label>
                @php
                    $badge = $invoice->status_badge;
                    $colors = [
                        'draft' => 'background: rgba(142, 142, 147, 0.3); color: rgba(142, 142, 147, 1);',
                        'sent' => 'background: rgba(0, 122, 255, 0.3); color: rgba(0, 122, 255, 1);',
                        'partial' => 'background: rgba(255, 149, 0, 0.3); color: rgba(255, 149, 0, 1);',
                        'paid' => 'background: rgba(52, 199, 89, 0.3); color: rgba(52, 199, 89, 1);',
                        'overdue' => 'background: rgba(255, 59, 48, 0.3); color: rgba(255, 59, 48, 1);',
                        'cancelled' => 'background: rgba(142, 142, 147, 0.3); color: rgba(142, 142, 147, 1);',
                    ];
                @endphp
                <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full" style="{{ $colors[$invoice->status] ?? $colors['draft'] }}">
                    {{ $badge['label'] }}
                </span>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Invoice Date</label>
                <p style="color: #FFFFFF;">{{ $invoice->invoice_date->format('d F Y') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Due Date</label>
                <p style="color: {{ $invoice->isOverdue() ? 'rgba(255, 59, 48, 1)' : '#FFFFFF' }};">
                    {{ $invoice->due_date->format('d F Y') }}
                    @if($invoice->isOverdue())
                    <span class="text-xs">(Overdue)</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Project & Client Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Project Info -->
        <div class="card-elevated rounded-apple-lg p-6">
            <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                <i class="fas fa-briefcase mr-2 text-apple-blue-dark"></i>Project
            </h3>
            <p class="font-semibold mb-2" style="color: rgba(0, 122, 255, 1);">{{ $invoice->project->name }}</p>
            @if($invoice->project->description)
            <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">{{ $invoice->project->description }}</p>
            @endif
        </div>

        <!-- Client Info -->
        <div class="card-elevated rounded-apple-lg p-6">
            <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                <i class="fas fa-user mr-2 text-apple-blue-dark"></i>Client
            </h3>
            <p class="font-semibold mb-2" style="color: #FFFFFF;">{{ $invoice->client_name ?? $invoice->project->client_name }}</p>
            @if($invoice->client_address)
            <p class="text-sm mb-2" style="color: rgba(235, 235, 245, 0.6);">{{ $invoice->client_address }}</p>
            @endif
            @if($invoice->client_tax_id)
            <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">NPWP: {{ $invoice->client_tax_id }}</p>
            @endif
        </div>
    </div>

    <!-- Invoice Items -->
    <div class="card-elevated rounded-apple-lg p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
            <i class="fas fa-list mr-2 text-apple-blue-dark"></i>Items
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr style="border-bottom: 2px solid rgba(58, 58, 60, 0.8);">
                        <th class="text-left py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">#</th>
                        <th class="text-left py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Description</th>
                        <th class="text-center py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Qty</th>
                        <th class="text-right py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Unit Price</th>
                        <th class="text-right py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $index => $item)
                    <tr style="border-bottom: 1px solid rgba(58, 58, 60, 0.4);">
                        <td class="py-3 px-2 text-sm" style="color: rgba(235, 235, 245, 0.6);">{{ $index + 1 }}</td>
                        <td class="py-3 px-2" style="color: rgba(235, 235, 245, 0.9);">{{ $item->description }}</td>
                        <td class="py-3 px-2 text-center" style="color: rgba(235, 235, 245, 0.9);">{{ $item->quantity }}</td>
                        <td class="py-3 px-2 text-right" style="color: rgba(235, 235, 245, 0.9);">
                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-2 text-right font-semibold" style="color: #FFFFFF;">
                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="mt-6 flex justify-end">
            <div class="w-full md:w-1/2 lg:w-1/3">
                <div class="space-y-3">
                    <div class="flex justify-between py-2">
                        <span style="color: rgba(235, 235, 245, 0.6);">Subtotal:</span>
                        <span class="font-semibold" style="color: #FFFFFF;">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span style="color: rgba(235, 235, 245, 0.6);">Tax ({{ number_format($invoice->tax_rate, 2) }}%):</span>
                        <span class="font-semibold" style="color: #FFFFFF;">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-3" style="border-top: 2px solid rgba(58, 58, 60, 0.8);">
                        <span class="text-lg font-bold" style="color: #FFFFFF;">Total:</span>
                        <span class="text-lg font-bold" style="color: rgba(0, 122, 255, 1);">
                            Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                    @if($invoice->paid_amount > 0)
                    <div class="flex justify-between py-2" style="border-top: 1px solid rgba(58, 58, 60, 0.4);">
                        <span style="color: rgba(235, 235, 245, 0.6);">Paid:</span>
                        <span class="font-semibold" style="color: rgba(52, 199, 89, 1);">
                            Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="font-semibold" style="color: rgba(255, 149, 0, 1);">Remaining:</span>
                        <span class="font-bold" style="color: rgba(255, 149, 0, 1);">
                            Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    @if($invoice->paymentSchedules && $invoice->paymentSchedules->count() > 0)
    <div class="card-elevated rounded-apple-lg p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
            <i class="fas fa-history mr-2 text-apple-blue-dark"></i>Payment History
        </h3>
        <div class="space-y-3">
            @foreach($invoice->paymentSchedules->sortByDesc('paid_date') as $payment)
            <div class="flex items-center justify-between p-4 rounded-lg" style="background: rgba(58, 58, 60, 0.5);">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(52, 199, 89, 0.2);">
                        <i class="fas fa-check-circle" style="color: rgba(52, 199, 89, 1);"></i>
                    </div>
                    <div>
                        <p class="font-medium" style="color: #FFFFFF;">{{ $payment->description }}</p>
                        <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">
                            {{ $payment->paid_date ? $payment->paid_date->format('d F Y') : '-' }}
                            @if($payment->payment_method)
                            • {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            @endif
                            @if($payment->reference_number)
                            • Ref: {{ $payment->reference_number }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold" style="color: rgba(52, 199, 89, 1);">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Notes -->
    @if($invoice->notes)
    <div class="card-elevated rounded-apple-lg p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
            <i class="fas fa-sticky-note mr-2 text-apple-blue-dark"></i>Notes
        </h3>
        <p style="color: rgba(235, 235, 245, 0.8);">{{ $invoice->notes }}</p>
    </div>
    @endif

    <!-- Actions -->
    @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
    <div class="card-elevated rounded-apple-lg p-6">
        <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
            <i class="fas fa-cog mr-2 text-apple-blue-dark"></i>Actions
        </h3>
        <div class="flex flex-wrap gap-3">
            @if($invoice->status !== 'paid')
            <button onclick="window.location.href='{{ route('projects.show', ['project' => $invoice->project_id, 'tab' => 'financial']) }}#payment-{{ $invoice->id }}'" 
                    class="px-4 py-2 rounded-lg font-medium transition-colors" 
                    style="background: rgba(52, 199, 89, 0.9); color: #FFFFFF;">
                <i class="fas fa-dollar-sign mr-2"></i>Record Payment
            </button>
            @endif
            @if($invoice->status === 'draft')
            <button onclick="updateStatus('sent')" 
                    class="px-4 py-2 rounded-lg font-medium transition-colors" 
                    style="background: rgba(0, 122, 255, 0.9); color: #FFFFFF;">
                <i class="fas fa-paper-plane mr-2"></i>Mark as Sent
            </button>
            <button onclick="updateStatus('cancelled')" 
                    class="px-4 py-2 rounded-lg font-medium transition-colors" 
                    style="background: rgba(255, 59, 48, 0.9); color: #FFFFFF;">
                <i class="fas fa-ban mr-2"></i>Cancel Invoice
            </button>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
function updateStatus(status) {
    if (!confirm(`Are you sure you want to update invoice status to ${status}?`)) return;
    
    fetch('{{ route("invoices.update-status", $invoice) }}', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload();
        } else {
            alert(result.message || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function sendInvoice() {
    if (!confirm('Kirim invoice ini ke client via email?')) return;

    const btn = document.querySelector('[onclick="sendInvoice()"]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...'; }

    fetch('{{ route("invoices.send", $invoice) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert(result.message || 'Gagal mengirim invoice');
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send Invoice'; }
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan saat mengirim invoice');
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send Invoice'; }
    });
}
</script>
@endsection
