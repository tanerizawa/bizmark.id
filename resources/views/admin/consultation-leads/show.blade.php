@extends('layouts.admin')

@section('title', "Lead Detail - #{$consultation->id}")

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start mb-6 space-y-3 sm:space-y-0">
        <div class="flex items-center">
            <a href="{{ route('admin.consultation-leads.index') }}" class="text-apple-blue hover:text-blue-400 mr-4">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-dark-text-primary">
                    Lead #{{ $consultation->id }}
                </h1>
                <div class="flex items-center space-x-3 text-xs mt-1 text-dark-text-secondary">
                    <span class="flex items-center">
                        <i class="fas fa-calendar-alt mr-1.5"></i>{{ $consultation->created_at->format('d M Y H:i') }}
                    </span>
                    @if($consultation->converted_to_client)
                        <span class="flex items-center text-purple-400">
                            <i class="fas fa-user-check mr-1.5"></i>Sudah jadi klien
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex space-x-2">
            @if(!$consultation->contacted)
                <form method="POST" action="{{ route('admin.consultation-leads.mark-contacted', $consultation) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded-apple text-sm font-medium transition-colors inline-flex items-center bg-green-500 hover:bg-green-600 text-white">
                        <i class="fas fa-phone mr-1.5"></i>Mark Contacted
                    </button>
                </form>
            @endif

            @if(!$consultation->converted_to_client)
                <button onclick="showConvertModal()" class="px-3 py-2 rounded-apple text-sm font-medium transition-colors inline-flex items-center bg-purple-500 hover:bg-purple-600 text-white">
                    <i class="fas fa-user-plus mr-1.5"></i>Konversi ke Klien
                </button>
            @endif

            <select onchange="updateStatus(this.value)" class="px-3 py-2 rounded-apple text-sm" style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                <option value="auto_estimated" {{ $consultation->estimate_status === 'auto_estimated' ? 'selected' : '' }}>Auto Estimated</option>
                <option value="reviewed" {{ $consultation->estimate_status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                <option value="approved" {{ $consultation->estimate_status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="quoted" {{ $consultation->estimate_status === 'quoted' ? 'selected' : '' }}>Quoted</option>
                <option value="rejected" {{ $consultation->estimate_status === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
    </div>

    <!-- Status Indicators -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="card-elevated rounded-apple-lg p-4 text-center">
            <div class="text-xs text-dark-text-secondary mb-2">Status</div>
            @php
                $statusColors = [
                    'auto_estimated' => 'bg-blue-500/20 text-blue-400 border-blue-500',
                    'reviewed' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500',
                    'approved' => 'bg-green-500/20 text-green-400 border-green-500',
                    'quoted' => 'bg-indigo-500/20 text-indigo-400 border-indigo-500',
                    'rejected' => 'bg-red-500/20 text-red-400 border-red-500',
                ];
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-apple text-sm font-medium border {{ $statusColors[$consultation->estimate_status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500' }}">
                {{ ucfirst(str_replace('_', ' ', $consultation->estimate_status)) }}
            </span>
        </div>

        <div class="card-elevated rounded-apple-lg p-4 text-center">
            <div class="text-xs text-dark-text-secondary mb-2">Status Kontak</div>
            @if($consultation->contacted)
                <span class="inline-flex items-center px-3 py-1 rounded-apple text-sm font-medium border bg-green-500/20 text-green-400 border-green-500">
                    <i class="fas fa-check-circle mr-1"></i>Dihubungi
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-apple text-sm font-medium border bg-orange-500/20 text-orange-400 border-orange-500">
                    <i class="fas fa-clock mr-1"></i>Belum Dihubungi
                </span>
            @endif
        </div>

        <div class="card-elevated rounded-apple-lg p-4 text-center">
            <div class="text-xs text-dark-text-secondary mb-2">Konversi</div>
            @if($consultation->converted_to_client)
                <span class="inline-flex items-center px-3 py-1 rounded-apple text-sm font-medium border bg-purple-500/20 text-purple-400 border-purple-500">
                    <i class="fas fa-user-check mr-1"></i>Converted
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-apple text-sm font-medium border bg-gray-500/20 text-gray-400 border-gray-500">
                    <i class="fas fa-user-clock mr-1"></i>Belum Converted
                </span>
            @endif
        </div>

        <div class="card-elevated rounded-apple-lg p-4 text-center">
            <div class="text-xs text-dark-text-secondary mb-2">Ukuran Bisnis</div>
            @php
                $sizeColors = [
                    'large' => 'bg-red-500/20 text-red-400 border-red-500',
                    'medium' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500',
                    'small' => 'bg-green-500/20 text-green-400 border-green-500',
                    'micro' => 'bg-gray-500/20 text-gray-400 border-gray-500',
                ];
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-apple text-sm font-medium border {{ $sizeColors[$consultation->business_size] ?? 'bg-gray-500/20 text-gray-400 border-gray-500' }}">
                {{ ucfirst($consultation->business_size) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Information -->
            <div class="card-elevated rounded-apple-lg">
                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <i class="fas fa-user mr-2 text-apple-blue"></i>Informasi Kontak
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="text-xs font-medium text-dark-text-secondary block mb-1">Nama Pengusul/Entitas</label>
                        <p class="text-sm text-dark-text-primary">
                            @if($consultation->name === 'Guest User' && isset($consultation->auto_estimate['form_data']['entity_type']))
                                <span class="text-yellow-400">Guest User</span> 
                                <span class="text-xs text-dark-text-secondary ml-2">({{ strtoupper($consultation->auto_estimate['form_data']['entity_type']) }} - {{ $consultation->auto_estimate['form_data']['business_nature'] ?? 'N/A' }})</span>
                            @else
                                {{ $consultation->name }}
                                @if(isset($consultation->auto_estimate['form_data']['entity_type']))
                                    <span class="text-xs text-dark-text-secondary ml-2">({{ strtoupper($consultation->auto_estimate['form_data']['entity_type']) }})</span>
                                @endif
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-dark-text-secondary block mb-1">Email</label>
                        <p class="text-sm">
                            @if(str_starts_with($consultation->email, 'guest-'))
                                <span class="text-yellow-400">{{ $consultation->email }}</span>
                                <span class="text-xs text-dark-text-secondary block mt-1">⚠️ No email provided - contact via phone</span>
                            @else
                                <span class="text-dark-text-primary">{{ $consultation->email }}</span>
                                <span class="text-xs text-green-400 block mt-1">✅ Email available for follow-up</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-dark-text-secondary block mb-1">Phone</label>
                        <p class="text-sm text-dark-text-primary">{{ $consultation->phone }}</p>
                    </div>
                    @if($consultation->company_name)
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Nama Perusahaan</label>
                            <p class="text-sm text-dark-text-primary">{{ $consultation->company_name }}</p>
                        </div>
                    @endif
                    @if(isset($consultation->auto_estimate['form_data']))
                        <div class="mt-4 p-3 rounded-apple" style="background-color: var(--dark-bg-tertiary);">
                            <label class="text-xs font-medium text-dark-text-secondary block mb-2">Additional Business Info</label>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-dark-text-secondary">Entity Type:</span>
                                    <span class="text-dark-text-primary ml-1">{{ strtoupper($consultation->auto_estimate['form_data']['entity_type'] ?? 'N/A') }}</span>
                                </div>
                                <div>
                                    <span class="text-dark-text-secondary">Business Nature:</span>
                                    <span class="text-dark-text-primary ml-1">{{ str_replace('_', ' ', ucfirst($consultation->auto_estimate['form_data']['business_nature'] ?? 'N/A')) }}</span>
                                </div>
                                <div>
                                    <span class="text-dark-text-secondary">Timeline:</span>
                                    <span class="text-dark-text-primary ml-1">{{ str_replace('_', ' ', ucfirst($consultation->auto_estimate['form_data']['target_timeline'] ?? 'N/A')) }}</span>
                                </div>
                                <div>
                                    <span class="text-dark-text-secondary">Region:</span>
                                    <span class="text-dark-text-primary ml-1">{{ str_replace('_', ' ', ucfirst($consultation->auto_estimate['form_data']['geographic_region'] ?? 'N/A')) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Business Information -->
            <div class="card-elevated rounded-apple-lg">
                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <i class="fas fa-building mr-2 text-apple-blue"></i>Informasi Bisnis
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">KBLI Code</label>
                            <p class="text-sm text-dark-text-primary">{{ $consultation->kbli_code }}</p>
                            <p class="text-xs text-dark-text-secondary mt-1">{{ optional($consultation->kbli)->description }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Investment Level</label>
                            @php
                                $actualInvestmentLevel = $consultation->auto_estimate['input_parameters']['investment_level'] ?? $consultation->investment_level;
                                $originalFormLevel = $actualInvestmentLevel;
                                
                                // Show original form data if different from stored DB value
                                $formInvestmentLabel = match($originalFormLevel) {
                                    'under_100m' => '< Rp 100 juta',
                                    '100m_500m' => 'Rp 100 - 500 juta',  
                                    '500m_2b' => 'Rp 500 juta - 2 miliar',
                                    '2b_10b' => 'Rp 2 - 10 miliar',
                                    '10b_50b' => 'Rp 10 - 50 miliar',
                                    'above_50b' => '> Rp 50 miliar',
                                    'over_2b' => '> Rp 2 miliar',
                                    default => 'Unknown',
                                };
                                
                                $investmentValue = $consultation->auto_estimate['investment_value'] ?? 0;
                            @endphp
                            <p class="text-sm text-dark-text-primary">
                                {{ $formInvestmentLabel }}
                                @if($originalFormLevel !== $consultation->investment_level)
                                    <span class="text-xs text-yellow-400 block mt-1">Original: {{ $originalFormLevel }} → DB: {{ $consultation->investment_level }}</span>
                                @endif
                                @if($investmentValue > 0)
                                    <span class="text-xs text-dark-text-secondary block mt-1">Est. Value: {{ 'Rp ' . number_format($investmentValue, 0, ',', '.') }}</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Location Type</label>
                            <p class="text-sm text-dark-text-primary">{{ ucfirst($consultation->location_type) }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Employee Count</label>
                            @php
                                $actualEmployeeCount = $consultation->auto_estimate['input_parameters']['employee_count'] ?? $consultation->employee_count;
                            @endphp
                            <p class="text-sm text-dark-text-primary">
                                {{ $actualEmployeeCount }} karyawan
                                @if($actualEmployeeCount != $consultation->employee_count)
                                    <span class="text-xs text-yellow-400 ml-2">(DB: {{ $consultation->employee_count }})</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Lokasi</label>
                            <p class="text-sm text-dark-text-primary">{{ $consultation->location ?: 'Tidak disebutkan' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Ukuran Bisnis</label>
                            @php
                                $actualBusinessSize = $consultation->auto_estimate['input_parameters']['business_size'] ?? $consultation->business_size;
                                $businessSizeLabel = match($actualBusinessSize) {
                                    'micro' => 'Mikro (< 10 karyawan)',
                                    'small' => 'Kecil (10-50 karyawan)',
                                    'medium' => 'Menengah (50-100 karyawan)',
                                    'large' => 'Besar (> 100 karyawan)',
                                    default => 'Unknown',
                                };
                            @endphp
                            <p class="text-sm text-dark-text-primary">
                                {{ $businessSizeLabel }}
                                @if($actualEmployeeCount && $actualEmployeeCount > 100 && $actualBusinessSize === 'medium')
                                    <span class="text-xs text-yellow-400 ml-2">(Should be "Large" with {{ $actualEmployeeCount }} employees)</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($consultation->project_description)
                        <div class="mt-4">
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Deskripsi Proyek</label>
                            <div class="text-sm text-dark-text-primary p-3 rounded-apple" style="background-color: var(--dark-bg-tertiary);">
                                {{ $consultation->project_description }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cost Estimation -->
            <div class="card-elevated rounded-apple-lg">
                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <i class="fas fa-calculator mr-2 text-apple-blue"></i>Estimasi Biaya
                        <span class="text-sm text-dark-text-secondary font-normal ml-2">
                            (Confidence: {{ number_format(($consultation->confidence_score ?? 0.5) * 100, 0) }}%)
                        </span>
                    </h3>
                </div>
                <div class="p-4">
                    @if(isset($consultation->auto_estimate['cost_summary']))
                        @php $costs = $consultation->auto_estimate['cost_summary']; @endphp
                        
                        <div class="space-y-2">
                            @if(isset($costs['formatted']['subtotal']))
                                <div class="flex justify-between items-center py-2" style="border-bottom: 1px solid rgba(84, 84, 88, 0.3);">
                                    <span class="text-sm text-dark-text-secondary">Subtotal</span>
                                    <span class="text-sm font-medium text-dark-text-primary">{{ $costs['formatted']['subtotal'] }}</span>
                                </div>
                            @endif
                            
                            @if(isset($costs['formatted']['overhead']) && $costs['overhead'] > 0)
                                <div class="flex justify-between items-center py-2" style="border-bottom: 1px solid rgba(84, 84, 88, 0.3);">
                                    <span class="text-sm text-dark-text-secondary">Overhead (10%)</span>
                                    <span class="text-sm font-medium text-apple-blue">+{{ number_format($costs['overhead']) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center py-3" style="border-top: 2px solid rgba(84, 84, 88, 0.5);">
                                <span class="text-sm font-semibold text-white">Grand Total</span>
                                <span class="text-lg font-bold text-green-400">{{ $costs['formatted']['grand_total'] ?? '-' }}</span>
                            </div>
                            
                            @if(isset($costs['cost_range']))
                                <div class="mt-3 p-3 rounded-apple text-center" style="background-color: rgba(10, 132, 255, 0.1);">
                                    <p class="text-xs text-dark-text-secondary mb-1">Rentang Estimasi</p>
                                    <p class="text-sm font-medium text-apple-blue">{{ $costs['formatted']['range'] ?? number_format($costs['cost_range']['min']) . ' - ' . number_format($costs['cost_range']['max']) }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center text-dark-text-secondary py-8">
                            <i class="fas fa-calculator text-2xl mb-3 block opacity-30"></i>
                            <p>Tidak ada estimasi biaya</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- AI Permit Recommendations -->
            @if(isset($consultation->auto_estimate['ai_recommendations']))
                <div class="card-elevated rounded-apple-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-dark-text-primary mb-4">AI Permit Recommendations</h3>
                        
                        @php $recommendations = $consultation->auto_estimate['ai_recommendations']; @endphp
                        
                        @if(isset($recommendations['permits']) && is_array($recommendations['permits']))
                            <div class="space-y-4">
                                @foreach($recommendations['permits'] as $index => $permit)
                                    <div class="bg-gray-50 rounded-apple p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-medium text-gray-900">{{ $permit['name'] ?? 'Unnamed Permit' }}</h4>
                                            @if(isset($permit['confidence']))
                                                <span class="text-xs px-2 py-1 rounded-full 
                                                    {{ $permit['confidence'] >= 0.8 ? 'bg-green-100 text-green-800' : 
                                                       ($permit['confidence'] >= 0.6 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ number_format($permit['confidence'] * 100, 0) }}% confidence
                                                </span>
                                            @endif
                                        </div>
                                        
                                        @if(isset($permit['description']))
                                            <p class="text-sm text-gray-600 mb-2">{{ $permit['description'] }}</p>
                                        @endif
                                        
                                        @if(isset($permit['category']))
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $permit['category'] }}
                                            </span>
                                        @endif

                                        @if(isset($permit['estimated_duration']))
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 ml-2">
                                                <i class="fas fa-clock mr-1"></i>{{ $permit['estimated_duration'] }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(isset($recommendations['summary']))
                            <div class="mt-4 p-4 bg-blue-50 rounded-apple">
                                <h5 class="font-medium text-blue-900 mb-2">Summary</h5>
                                <p class="text-sm text-blue-800">{{ $recommendations['summary'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Admin Notes -->
            <div class="card-elevated rounded-apple-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-dark-text-primary mb-4">Admin Notes</h3>
                    
                    <!-- Add Note Form -->
                    <form method="POST" action="{{ route('admin.consultation-leads.add-note', $consultation) }}" class="mb-4">
                        @csrf
                        <textarea name="note" rows="3" placeholder="Add a note..." 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-apple text-sm mb-2" required></textarea>
                        <button type="submit" class="btn-primary px-3 py-2 rounded-apple text-sm">Add Note</button>
                    </form>

                    <!-- Notes List -->
                    @if($consultation->admin_notes)
                        @php
                            // Handle both old format (string) and new format (array)
                            $notes = is_array($consultation->admin_notes) ? $consultation->admin_notes : [];
                            if (is_string($consultation->admin_notes) && !empty($consultation->admin_notes)) {
                                // Convert old string format to array for display
                                $notes = [['note' => $consultation->admin_notes, 'admin_name' => 'Admin', 'created_at' => $consultation->updated_at->toISOString()]];
                            }
                        @endphp
                        
                        @if(count($notes) > 0)
                            <div class="space-y-3 max-h-64 overflow-y-auto">
                                @foreach(array_reverse($notes) as $note)
                                    <div class="bg-gray-50 rounded-apple p-3">
                                        <p class="text-sm text-gray-900">{{ $note['note'] ?? $note }}</p>
                                        <div class="text-xs text-gray-500 mt-2">
                                            <i class="fas fa-user mr-1"></i>{{ $note['admin_name'] ?? 'Admin' }} • 
                                            {{ isset($note['created_at']) ? \Carbon\Carbon::parse($note['created_at'])->diffForHumans() : 'Unknown time' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-4">
                                <i class="fas fa-sticky-note text-2xl text-gray-300 mb-2"></i>
                                <p class="text-sm">No notes yet</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-gray-500 py-4">
                            <i class="fas fa-sticky-note text-2xl text-gray-300 mb-2"></i>
                            <p class="text-sm">No notes yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="card-elevated rounded-apple-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-dark-text-primary mb-4">Activity Timeline</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus text-blue-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Consultation submitted</p>
                                <p class="text-xs text-gray-500">{{ $consultation->created_at->format('M d, Y \a\t H:i') }}</p>
                            </div>
                        </div>

                        @if($consultation->contacted)
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-phone text-green-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Marked as contacted</p>
                                    <p class="text-xs text-gray-500">{{ $consultation->contacted_at ? $consultation->contacted_at->format('M d, Y \a\t H:i') : 'No date recorded' }}</p>
                                </div>
                            </div>
                        @endif

                        @if($consultation->converted_to_client)
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-check text-purple-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Converted to client</p>
                                    <p class="text-xs text-gray-500">{{ $consultation->converted_at ? $consultation->converted_at->format('M d, Y \a\t H:i') : 'No date recorded' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Information -->

            <!-- RAG AI Insights -->
            @if($consultation->rag_insights)
                @php
                    $ragData = is_array($consultation->rag_insights) ? $consultation->rag_insights : json_decode($consultation->rag_insights, true);
                @endphp
                <div class="card-elevated rounded-apple-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-dark-text-primary mb-4 flex items-center">
                            <i class="fas fa-brain text-purple-600 mr-2"></i>
                            AI Regulation Insights
                        </h3>

                        <!-- Confidence Score -->
                        @if($consultation->rag_confidence)
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Confidence Score</span>
                                    <span class="text-sm font-semibold {{ $consultation->rag_confidence >= 0.8 ? 'text-green-600' : ($consultation->rag_confidence >= 0.6 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ number_format($consultation->rag_confidence * 100, 0) }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $consultation->rag_confidence >= 0.8 ? 'bg-green-500' : ($consultation->rag_confidence >= 0.6 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                         style="width: {{ $consultation->rag_confidence * 100 }}%"></div>
                                </div>
                            </div>
                        @endif

                        <!-- AI Answer -->
                        @if(isset($ragData['answer']))
                            <div class="mb-4">
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Regulatory Context</label>
                                <div class="mt-1 p-3 bg-purple-50 rounded-apple text-sm text-purple-900 leading-relaxed">
                                    {{ $ragData['answer'] }}
                                </div>
                            </div>
                        @endif

                        @if(isset($ragData['activity_requirements']) && count($ragData['activity_requirements']) > 0)
                            <div class="mb-4">
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">KBLI Activity Requirements</label>
                                <div class="mt-2 space-y-2">
                                    @foreach($ragData['activity_requirements'] as $activity)
                                        <div class="p-3 bg-fuchsia-50 rounded-apple border border-fuchsia-100">
                                            <div class="flex items-center justify-between gap-3 mb-1">
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-gray-900 truncate">
                                                        {{ $activity['label'] ?? 'Activity' }}
                                                        @if(isset($activity['kbli_code']))
                                                            <span class="font-mono text-xs text-fuchsia-700">({{ $activity['kbli_code'] }})</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-gray-600 truncate">{{ $activity['description'] ?? '-' }}</div>
                                                </div>
                                                @if(isset($activity['confidence']))
                                                    <span class="text-xs font-semibold {{ $activity['confidence'] >= 0.8 ? 'text-green-600' : ($activity['confidence'] >= 0.6 ? 'text-yellow-600' : 'text-red-600') }}">
                                                        {{ number_format($activity['confidence'] * 100, 0) }}%
                                                    </span>
                                                @endif
                                            </div>
                                            @if(isset($activity['answer']))
                                                <div class="text-xs text-gray-700 leading-relaxed">{{ \Illuminate\Support\Str::limit(strip_tags($activity['answer']), 200) }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Source Documents -->
                        @if(isset($ragData['sources']) && count($ragData['sources']) > 0)
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Source Documents ({{ count($ragData['sources']) }})
                                </label>
                                <div class="mt-2 space-y-2">
                                    @foreach($ragData['sources'] as $source)
                                        <div class="flex items-start p-2 bg-gray-50 rounded-apple">
                                            <i class="fas fa-file-alt text-purple-400 mt-1 mr-2 flex-shrink-0"></i>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-gray-900 truncate">{{ $source['title'] ?? 'Unknown' }}</div>
                                                @if(isset($source['section']))
                                                    <div class="text-xs text-gray-500">{{ $source['section'] }}</div>
                                                @endif
                                                @if(isset($source['confidence']))
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium mt-1
                                                        {{ $source['confidence'] >= 0.8 ? 'bg-green-100 text-green-700' : ($source['confidence'] >= 0.6 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                        {{ number_format($source['confidence'] * 100, 0) }}% relevan
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($consultation->rag_processed_at)
                            <div class="mt-3 text-xs text-gray-400">
                                <i class="fas fa-clock mr-1"></i>Processed {{ \Carbon\Carbon::parse($consultation->rag_processed_at)->diffForHumans() }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            @if($consultation->converted_to_client && isset($consultation->client_id))
                <div class="card-elevated rounded-apple-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-dark-text-primary mb-4">Related Client</h3>
                        
                        @if($consultation->client)
                            <div class="space-y-2">
                                <p class="text-sm"><strong>Client ID:</strong> {{ $consultation->client->id }}</p>
                                <p class="text-sm"><strong>Email:</strong> {{ $consultation->client->email }}</p>
                                @if($consultation->client->company_name)
                                    <p class="text-sm"><strong>Company:</strong> {{ $consultation->client->company_name }}</p>
                                @endif
                                <a href="{{ route('admin.clients.show', $consultation->client) }}" 
                                   class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-external-link-alt mr-1"></i>View Client
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Convert to Client Modal -->
<div id="convertModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900">Convert to Client</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Convert this consultation lead to a client account?
                </p>
            </div>
            <form method="POST" action="{{ route('admin.consultation-leads.convert-to-client', $consultation) }}" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="create_client_account" value="1" class="form-checkbox" checked>
                        <span class="ml-2 text-sm">Create client account</span>
                    </label>
                </div>
                <div class="mb-4">
                    <input type="password" name="password" placeholder="Password for client" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                </div>
                <div class="mb-4">
                    <input type="text" name="company_name" placeholder="Company name (optional)" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="flex justify-center space-x-4">
                    <button type="submit" class="btn-primary px-4 py-2 rounded-md">Convert</button>
                    <button type="button" onclick="hideConvertModal()" class="btn-secondary px-4 py-2 rounded-md">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showConvertModal() {
    document.getElementById('convertModal').classList.remove('hidden');
}

function hideConvertModal() {
    document.getElementById('convertModal').classList.add('hidden');
}

function updateStatus(newStatus) {
    if(confirm('Update status to: ' + newStatus.replace('_', ' ') + '?')) {
        fetch(`{{ route('admin.consultation-leads.update-status', $consultation) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                console.error('Error updating status:', data);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            location.reload();
        });
    }
}

// Close modal when clicking outside
document.getElementById('convertModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideConvertModal();
    }
});
</script>
@endpush