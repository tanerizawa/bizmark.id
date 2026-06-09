@extends('layouts.admin')

@section('title', 'Mulai Rekonsiliasi Baru')
@section('page-title', 'Mulai Rekonsiliasi Bank Baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Validation Errors Alert (Global) -->
    @if ($errors->any())
    <div class="mb-6 rounded-apple p-4 bg-apple-red/10 border border-apple-red/30">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle mt-0.5 mr-3 text-apple-red"></i>
            <div class="flex-1">
                <p class="text-sm font-medium mb-2 text-apple-red">Validation Error</p>
                <ul class="text-xs space-y-1 text-dark-text-primary">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('reconciliations.index') }}" 
           class="inline-flex items-center text-sm text-apple-blue">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Rekonsiliasi
        </a>
    </div>

    <!-- Form Card -->
    <div class="rounded-apple p-6 shadow-sm bg-white/5 backdrop-blur-md">
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-2 text-dark-text-primary">Informasi Rekonsiliasi</h2>
            <p class="text-sm text-dark-text-secondary">
                Upload bank statement dan mulai proses rekonsiliasi dengan transaksi sistem
            </p>
        </div>

        <form action="{{ route('reconciliations.store') }}" method="POST" enctype="multipart/form-data" id="reconciliationForm">
            @csrf

            <!-- Cash Account Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2 text-dark-text-primary">
                    Akun Kas/Bank <span class="text-red-500">*</span>
                </label>
                <select name="cash_account_id" required
                        class="w-full px-4 py-3 rounded-apple text-sm border-none focus:outline-none focus:ring-2 focus:ring-blue-500 @error('cash_account_id') ring-2 ring-red-500 @enderror"
                        class="w-full px-4 py-3 rounded-apple text-sm border-none focus:outline-none focus:ring-2 focus:ring-blue-500 @error('cash_account_id') ring-2 ring-red-500 @enderror bg-white/5 text-dark-text-primary backdrop-blur-md">
                    <option value="">Pilih akun kas/bank</option>
                    @foreach($cashAccounts as $account)
                        <option value="{{ $account->id }}" {{ old('cash_account_id') == $account->id ? 'selected' : '' }}>
                            {{ $account->account_name }} - {{ $account->bank_name }} ({{ $account->account_number }})
                        </option>
                    @endforeach
                </select>
                @error('cash_account_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Period Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="w-full px-4 py-3 rounded-apple text-sm border-none focus:outline-none focus:ring-2 focus:ring-blue-500 @error('start_date') ring-2 ring-red-500 @enderror"
                           class="w-full px-4 py-3 rounded-apple text-sm border-none focus:outline-none focus:ring-2 focus:ring-blue-500 @error('start_date') ring-2 ring-red-500 @enderror bg-white/5 text-dark-text-primary backdrop-blur-md">
                    @error('start_date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary">
                        Tanggal Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                           class="w-full px-4 py-3 rounded-apple text-sm border-none focus:outline-none focus:ring-2 focus:ring-blue-500 @error('end_date') ring-2 ring-red-500 @enderror"
                           class="w-full px-4 py-3 rounded-apple text-sm border-none focus:outline-none focus:ring-2 focus:ring-blue-500 @error('end_date') ring-2 ring-red-500 @enderror bg-white/5 text-dark-text-primary backdrop-blur-md">
                    @error('end_date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Bank Balances -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary">
                        Saldo Awal Bank
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-sm text-dark-text-secondary">Rp</span>
                        <input type="text" id="opening_balance_display"
                               class="w-full pl-12 pr-4 py-3 rounded-apple text-sm border-none focus:outline-none focus:ring-2 focus:ring-blue-500 @error('opening_balance_bank') ring-2 ring-red-500 @enderror bg-white/5 text-dark-text-primary backdrop-blur-md"
                               placeholder="0.00"
                               value="{{ old('opening_balance_bank') ? number_format((float)old('opening_balance_bank'), 2, '.', ',') : '' }}">
                        <input type="hidden" name="opening_balance_bank" id="opening_balance_bank" value="{{ old('opening_balance_bank') }}">
                    </div>
                    @error('opening_balance_bank')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-dark-text-tertiary/80">
                        <i class="fas fa-magic mr-1"></i>Auto-extract dari CSV BCA atau input manual
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary">
                        Saldo Akhir Bank
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-sm text-dark-text-secondary">Rp</span>
                        <input type="text" id="closing_balance_display"
                               class="w-full pl-12 pr-4 py-3 rounded-apple text-sm border-none focus:outline-none focus:ring-2 focus:ring-blue-500 @error('closing_balance_bank') ring-2 ring-red-500 @enderror bg-white/5 text-dark-text-primary backdrop-blur-md"
                               placeholder="0.00"
                               value="{{ old('closing_balance_bank') ? number_format((float)old('closing_balance_bank'), 2, '.', ',') : '' }}">
                        <input type="hidden" name="closing_balance_bank" id="closing_balance_bank" value="{{ old('closing_balance_bank') }}">
                    </div>
                    @error('closing_balance_bank')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-dark-text-tertiary/80">
                        <i class="fas fa-magic mr-1"></i>Auto-extract dari CSV BCA atau input manual
                    </p>
                </div>
            </div>

            <!-- Bank Statement Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2 text-dark-text-primary">
                    Upload Bank Statement <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-dashed rounded-apple p-6 text-center hover:border-blue-500 transition-colors cursor-pointer @error('bank_statement') border-red-500 @enderror border-white/20 bg-white/[0.02]"
                     id="dropZone">
                    <input type="file" name="bank_statement" id="bankStatementInput" accept=".csv,.xlsx,.xls" required class="hidden">
                    <div id="uploadPrompt">
                        <i class="fas fa-cloud-upload-alt text-4xl mb-3 text-apple-blue/60"></i>
                        <p class="text-sm font-medium mb-1 text-dark-text-primary">
                            Drag & drop file atau klik untuk browse
                        </p>
                        <p class="text-xs text-dark-text-tertiary/80">
                            Format: CSV atau Excel (.xlsx, .xls) - Max 5MB
                        </p>
                    </div>
                    <div id="uploadSuccess" class="hidden">
                        <i class="fas fa-file-csv text-4xl mb-3 text-apple-green"></i>
                        <p class="text-sm font-medium text-apple-green" id="fileName"></p>
                        <button type="button" onclick="clearFile()" class="text-xs mt-2 text-dark-text-secondary">
                            <i class="fas fa-times"></i> Ganti file
                        </button>
                    </div>
                </div>
                @error('bank_statement')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bank Format Info -->
            <div class="rounded-apple p-4 mb-6 bg-apple-blue/10">
                <div class="flex items-start">
                    <i class="fas fa-info-circle mt-0.5 mr-3 text-apple-blue"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium mb-3 text-apple-blue">Format Bank Statement yang Didukung</p>
                        
                        <!-- BCA Format -->
                        <div class="mb-3">
                            <p class="text-xs font-semibold mb-1 text-dark-text-primary">
                                <i class="fas fa-check-circle mr-1 text-apple-green"></i>Format BCA (Auto-Detect)
                            </p>
                            <p class="text-xs mb-2 text-dark-text-secondary/90">
                                File CSV dari BCA akan otomatis terdeteksi dan di-parse. Saldo awal & akhir akan otomatis terisi dari footer CSV.
                            </p>
                            <code class="text-xs block p-2 rounded bg-black/20 text-dark-text-primary">
Account No.,=,'1091806504<br>
Date,Description,Branch,Amount,,Balance<br>
'07/09,TRANSFER...,'0998,500000.00,DB,1234567.89<br>
Starting Balance,=,1408847.23<br>
Ending Balance,=,178447.23
                            </code>
                        </div>
                        
                        <!-- Standard/BTN Format -->
                        <div>
                            <p class="text-xs font-semibold mb-1 text-dark-text-primary">
                                <i class="fas fa-check-circle mr-1 text-apple-green"></i>Format Standard/BTN
                            </p>
                            <p class="text-xs mb-2 text-dark-text-secondary/90">
                                Untuk bank lain, gunakan format standard dengan kolom terpisah untuk Debit dan Kredit. Saldo perlu diinput manual.
                            </p>
                            <code class="text-xs block p-2 rounded bg-black/20 text-dark-text-primary">
Tanggal,Keterangan,Debet,Kredit,Saldo,Referensi<br>
2025-09-07,TRANSFER MASUK,0,500000.00,1234567.89,REF001
                            </code>
                        </div>
                        
                        <div class="mt-3 pt-3 border-t border-apple-blue/20">
                            <p class="text-xs text-dark-text-secondary">
                                <strong>Format Angka:</strong> Gunakan koma (,) untuk separator ribuan dan titik (.) untuk desimal
                            </p>
                            <p class="text-xs mt-1 text-dark-text-tertiary/80">
                                <strong>Contoh:</strong> 1,234,567.89 atau 42,485,447.23
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('reconciliations.index') }}"
                   class="px-6 py-3 rounded-apple text-sm font-medium transition-all hover:opacity-90 bg-white/5 text-dark-text-primary">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-3 rounded-apple text-sm font-medium shadow-sm transition-all hover:opacity-90 btn-primary">
                    <i class="fas fa-check mr-2"></i>
                    Mulai Rekonsiliasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// CSS Animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
`;
document.head.appendChild(style);

// Currency formatting functions
function formatCurrency(value) {
    // Remove all non-digit and non-decimal characters
    let numStr = value.replace(/[^\d.]/g, '');
    
    // Handle multiple decimal points
    const parts = numStr.split('.');
    if (parts.length > 2) {
        numStr = parts[0] + '.' + parts.slice(1).join('');
    }
    
    // Limit to 2 decimal places
    if (parts.length === 2 && parts[1].length > 2) {
        numStr = parts[0] + '.' + parts[1].substring(0, 2);
    }
    
    // Parse to number
    const num = parseFloat(numStr);
    if (isNaN(num)) return '';
    
    // Format with thousand separators and 2 decimals
    return num.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function parseCurrency(formattedValue) {
    // Remove commas and parse to float
    return parseFloat(formattedValue.replace(/,/g, '')) || 0;
}

// Setup currency input fields
function setupCurrencyInput(displayInputId, hiddenInputId) {
    const displayInput = document.getElementById(displayInputId);
    const hiddenInput = document.getElementById(hiddenInputId);
    
    displayInput.addEventListener('input', function(e) {
        let value = e.target.value;
        
        // Remove formatting for processing
        let rawValue = value.replace(/,/g, '');
        
        // Store cursor position
        let cursorPos = e.target.selectionStart;
        let commasBefore = (value.substring(0, cursorPos).match(/,/g) || []).length;
        
        // Format the value
        let formatted = formatCurrency(rawValue);
        e.target.value = formatted;
        
        // Update hidden input with raw number value
        let parsedValue = parseCurrency(formatted);
        hiddenInput.value = parsedValue;
        
        // Restore cursor position (adjust for added/removed commas)
        let commasAfter = (formatted.substring(0, cursorPos).match(/,/g) || []).length;
        let newCursorPos = cursorPos + (commasAfter - commasBefore);
        e.target.setSelectionRange(newCursorPos, newCursorPos);
    });
    
    displayInput.addEventListener('blur', function(e) {
        if (e.target.value) {
            e.target.value = formatCurrency(e.target.value);
            hiddenInput.value = parseCurrency(e.target.value);
        }
    });
    
    // Handle paste
    displayInput.addEventListener('paste', function(e) {
        setTimeout(() => {
            let formatted = formatCurrency(e.target.value);
            e.target.value = formatted;
            hiddenInput.value = parseCurrency(formatted);
        }, 10);
    });
}

// Initialize currency inputs
setupCurrencyInput('opening_balance_display', 'opening_balance_bank');
setupCurrencyInput('closing_balance_display', 'closing_balance_bank');

// Form submit handler - ensure hidden inputs are filled
document.getElementById('reconciliationForm').addEventListener('submit', function(e) {
    const openingDisplay = document.getElementById('opening_balance_display');
    const openingHidden = document.getElementById('opening_balance_bank');
    const closingDisplay = document.getElementById('closing_balance_display');
    const closingHidden = document.getElementById('closing_balance_bank');
    
    // Update hidden inputs before submit
    if (openingDisplay.value) {
        openingHidden.value = parseCurrency(openingDisplay.value);
    }
    if (closingDisplay.value) {
        closingHidden.value = parseCurrency(closingDisplay.value);
    }
    
    // Validate that hidden inputs have values (allow 0 for BCA auto-extract edge cases)
    if (!openingHidden.value && openingHidden.value !== '0') {
        e.preventDefault();
        showNotification('⚠️ Saldo Awal Bank harus diisi. Upload CSV BCA untuk auto-fill atau input manual.', 'warning');
        openingDisplay.focus();
        return false;
    }
    
    if (!closingHidden.value && closingHidden.value !== '0') {
        e.preventDefault();
        showNotification('⚠️ Saldo Akhir Bank harus diisi. Upload CSV BCA untuk auto-fill atau input manual.', 'warning');
        closingDisplay.focus();
        return false;
    }
    
    console.log('Form submitting with values:', {
        opening: openingHidden.value,
        closing: closingHidden.value
    });
    
    return true;
});

// File upload handling
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('bankStatementInput');
const uploadPrompt = document.getElementById('uploadPrompt');
const uploadSuccess = document.getElementById('uploadSuccess');
const fileNameDisplay = document.getElementById('fileName');

dropZone.addEventListener('click', () => fileInput.click());

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = 'rgba(0, 122, 255, 1)';
});

dropZone.addEventListener('dragleave', () => {
    dropZone.style.borderColor = 'rgba(235, 235, 245, 0.2)';
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = 'rgba(235, 235, 245, 0.2)';
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        showFileName(files[0].name);
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        const file = e.target.files[0];
        showFileName(file.name);
        
        // Try to auto-extract balances from BCA CSV
        if (file.name.toLowerCase().endsWith('.csv')) {
            extractBCABalances(file);
        }
    }
});

// Extract balances from BCA CSV format
function extractBCABalances(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const content = e.target.result;
        const lines = content.split('\n');
        
        // Check if this is BCA format (first line contains "Account No.")
        if (lines[0] && lines[0].includes('Account No')) {
            console.log('BCA format detected, extracting balances...');
            
            let startingBalance = null;
            let endingBalance = null;
            
            // Look for footer rows (usually last 4-6 lines)
            for (let i = Math.max(0, lines.length - 10); i < lines.length; i++) {
                const line = lines[i].trim();
                
                // Starting Balance row
                if (line.includes('Starting Balance') || line.includes('Saldo Awal')) {
                    const parts = line.split(',');
                    if (parts.length >= 3) {
                        // Format: Starting Balance,=,1408847.23
                        const value = parts[2].replace(/['"]/g, '').trim();
                        startingBalance = parseFloat(value);
                    }
                }
                
                // Ending Balance row
                if (line.includes('Ending Balance') || line.includes('Saldo Akhir')) {
                    const parts = line.split(',');
                    if (parts.length >= 3) {
                        // Format: Ending Balance,=,178447.23
                        const value = parts[2].replace(/['"]/g, '').trim();
                        endingBalance = parseFloat(value);
                    }
                }
            }
            
            // Update form fields if values found
            if (startingBalance !== null && !isNaN(startingBalance)) {
                document.getElementById('opening_balance_display').value = formatCurrency(startingBalance.toString());
                document.getElementById('opening_balance_bank').value = startingBalance;
                console.log('Starting balance extracted:', startingBalance);
                
                // Show success message
                showBalanceExtractedMessage('Saldo Awal', startingBalance);
            }
            
            if (endingBalance !== null && !isNaN(endingBalance)) {
                document.getElementById('closing_balance_display').value = formatCurrency(endingBalance.toString());
                document.getElementById('closing_balance_bank').value = endingBalance;
                console.log('Ending balance extracted:', endingBalance);
                
                // Show success message
                showBalanceExtractedMessage('Saldo Akhir', endingBalance);
            }
            
            if (startingBalance !== null || endingBalance !== null) {
                // Show notification
                showNotification('✅ Format BCA terdeteksi! Saldo otomatis terisi dari CSV.', 'success');
            }
        } else {
            console.log('Standard format detected (not BCA)');
            showNotification('ℹ️ Format standard terdeteksi. Silakan input saldo manual.', 'info');
        }
    };
    
    reader.readAsText(file);
}

// Show balance extracted message
function showBalanceExtractedMessage(label, value) {
    console.log(`${label} auto-extracted: Rp ${formatCurrency(value.toString())}`);
}

// Show notification toast
function showNotification(message, type = 'info') {
    // Remove existing notification
    const existing = document.getElementById('autoNotification');
    if (existing) existing.remove();
    
    const bgColors = {
        success: 'rgba(52, 199, 89, 0.95)',
        info: 'rgba(0, 122, 255, 0.95)',
        warning: 'rgba(255, 159, 10, 0.95)',
        error: 'rgba(255, 59, 48, 0.95)'
    };
    
    const notification = document.createElement('div');
    notification.id = 'autoNotification';
    notification.className = 'fixed bottom-4 right-4 px-6 py-3 rounded-apple shadow-lg z-50 animate-fade-in';
    notification.style.background = bgColors[type] || bgColors.info;
    notification.style.color = 'white';
    notification.style.animation = 'slideInRight 0.3s ease-out';
    notification.innerHTML = message;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function showFileName(name) {
    fileNameDisplay.textContent = name;
    uploadPrompt.classList.add('hidden');
    uploadSuccess.classList.remove('hidden');
}

function clearFile() {
    fileInput.value = '';
    uploadPrompt.classList.remove('hidden');
    uploadSuccess.classList.add('hidden');
}
</script>

@if(session('error'))
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-apple shadow-lg z-50 animate-fade-in">
    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
</div>
@endif
@endsection
