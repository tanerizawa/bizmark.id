@extends('layouts.admin')

@section('title', 'Buat Quotation - ' . $application->application_number)

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.permit-applications.show', $application->id) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Buat Quotation</h1>
                <p class="text-gray-600 mt-1">{{ $application->application_number }} - {{ $application->permitType?->name ?? 'Tipe Izin Tidak Tersedia' }}</p>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Client Info Card -->
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <div class="flex justify-between">
                <div>
                    <p class="text-sm text-gray-600">Client</p>
                    <p class="font-semibold text-gray-900">{{ $application->client->name }}</p>
                    <p class="text-sm text-gray-600">{{ $application->client->email }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Harga Dasar</p>
                    <p class="font-semibold text-gray-900">Rp {{ number_format($application->permitType?->base_price ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Quotation Form -->
        <form action="{{ route('admin.quotations.store') }}" method="POST" id="quotationForm" class="bg-white rounded-lg shadow p-6">
            @csrf
            <input type="hidden" name="application_id" value="{{ $application->id }}">

            <!-- Base Price -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Harga Dasar <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                    <input 
                        type="number" 
                        name="base_price" 
                        id="basePrice"
                        value="{{ old('base_price', $application->permitType?->base_price ?? 0) }}"
                        required
                        min="0"
                        step="1000"
                        class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        onchange="calculateTotal()"
                    >
                </div>
                @error('base_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Additional Fees -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <label class="block text-sm font-medium text-gray-700">
                        Biaya Tambahan (Optional)
                    </label>
                    <button 
                        type="button" 
                        onclick="addFeeRow()"
                        class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700"
                    >
                        <i class="fas fa-plus mr-1"></i>Tambah
                    </button>
                </div>
                
                <div id="feesContainer" class="space-y-3">
                    <!-- Fee rows will be added here dynamically -->
                </div>
            </div>

            <!-- Tax -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pajak (%) <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    name="tax_percentage" 
                    id="taxPercentage"
                    value="{{ old('tax_percentage', 11) }}"
                    required
                    min="0"
                    max="100"
                    step="0.1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    onchange="calculateTotal()"
                >
                @error('tax_percentage')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Down Payment -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    DP (%) <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    name="down_payment_percentage" 
                    id="dpPercentage"
                    value="{{ old('down_payment_percentage', 30) }}"
                    required
                    min="0"
                    max="100"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    onchange="calculateTotal()"
                >
                @error('down_payment_percentage')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Validity Days -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Masa Berlaku (Hari) <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    name="validity_days" 
                    value="{{ old('validity_days', 30) }}"
                    required
                    min="1"
                    max="365"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                >
                @error('validity_days')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan / Syarat & Ketentuan
                </label>
                <textarea 
                    name="notes" 
                    rows="5"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    placeholder="Contoh: Pembayaran dilakukan via transfer bank..."
                >{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Summary -->
            <div class="border-t pt-6">
                <h3 class="font-bold text-gray-900 mb-4">Ringkasan Biaya</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Harga Dasar</span>
                        <span class="font-semibold" id="displayBasePrice">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Biaya Tambahan</span>
                        <span class="font-semibold" id="displayAdditionalFees">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-base font-semibold">
                        <span class="text-gray-700">Subtotal</span>
                        <span id="displaySubtotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pajak (<span id="displayTaxPercent">11</span>%)</span>
                        <span class="font-semibold" id="displayTax">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t">
                        <span class="text-gray-900">TOTAL</span>
                        <span class="text-purple-600" id="displayTotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-orange-600 font-semibold">
                        <span>DP (<span id="displayDpPercent">30</span>%)</span>
                        <span id="displayDp">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Sisa Pembayaran</span>
                        <span class="font-semibold" id="displayRemaining">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex gap-3 mt-6">
                <button 
                    type="submit"
                    class="flex-1 px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold"
                >
                    <i class="fas fa-check mr-2"></i>Buat Quotation
                </button>
                <a 
                    href="{{ route('admin.permit-applications.show', $application->id) }}"
                    class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold"
                >
                    Batal
                </a>
            </div>
        </form>

    </div>
</div>

<script>
let feeCounter = 0;

function addFeeRow() {
    const container = document.getElementById('feesContainer');
    const row = document.createElement('div');
    row.className = 'flex gap-2';
    row.id = `feeRow${feeCounter}`;
    row.innerHTML = `
        <input 
            type="text" 
            name="additional_fees[${feeCounter}][description]" 
            placeholder="Deskripsi biaya"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            onchange="calculateTotal()"
        >
        <div class="relative">
            <span class="absolute left-3 top-2.5 text-gray-500 text-sm">Rp</span>
            <input 
                type="number" 
                name="additional_fees[${feeCounter}][amount]" 
                placeholder="0"
                min="0"
                step="1000"
                class="w-48 pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent fee-amount"
                onchange="calculateTotal()"
            >
        </div>
        <button 
            type="button" 
            onclick="removeFeeRow(${feeCounter})"
            class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200"
        >
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(row);
    feeCounter++;
}

function removeFeeRow(id) {
    document.getElementById(`feeRow${id}`).remove();
    calculateTotal();
}

function calculateTotal() {
    // Get base price
    const basePrice = parseFloat(document.getElementById('basePrice').value) || 0;
    
    // Calculate additional fees
    const feeAmounts = document.querySelectorAll('.fee-amount');
    let additionalFees = 0;
    feeAmounts.forEach(input => {
        additionalFees += parseFloat(input.value) || 0;
    });
    
    // Calculate subtotal
    const subtotal = basePrice + additionalFees;
    
    // Calculate tax
    const taxPercent = parseFloat(document.getElementById('taxPercentage').value) || 0;
    const taxAmount = subtotal * (taxPercent / 100);
    
    // Calculate total
    const total = subtotal + taxAmount;
    
    // Calculate DP
    const dpPercent = parseFloat(document.getElementById('dpPercentage').value) || 0;
    const dpAmount = total * (dpPercent / 100);
    const remaining = total - dpAmount;
    
    // Update display
    document.getElementById('displayBasePrice').textContent = formatCurrency(basePrice);
    document.getElementById('displayAdditionalFees').textContent = formatCurrency(additionalFees);
    document.getElementById('displaySubtotal').textContent = formatCurrency(subtotal);
    document.getElementById('displayTaxPercent').textContent = taxPercent.toFixed(1);
    document.getElementById('displayTax').textContent = formatCurrency(taxAmount);
    document.getElementById('displayTotal').textContent = formatCurrency(total);
    document.getElementById('displayDpPercent').textContent = dpPercent;
    document.getElementById('displayDp').textContent = formatCurrency(dpAmount);
    document.getElementById('displayRemaining').textContent = formatCurrency(remaining);
}

function formatCurrency(amount) {
    return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
});
</script>
@endsection
