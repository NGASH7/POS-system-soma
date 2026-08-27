@extends('layouts.app')

@section('header_title', 'Create Discount')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner max-w-5xl">

        <!-- Page Header -->
        <div class="flex items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('discounts.index') }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-900 hover:border-slate-300 flex items-center justify-center transition-colors">
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Create Discount</h1>
                    <p class="text-sm text-slate-500">Configure a new promotional campaign or coupon code</p>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50/80 p-4 text-sm text-rose-800">
                <div class="font-semibold flex items-center gap-2 mb-1">
                    <i class="fas fa-exclamation-triangle text-rose-500"></i>
                    <span>Please correct the errors below:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('discounts.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left 2 Cols: Main Form -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Basic Details Card -->
                    <div class="soma-card p-6">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            <span>General Information</span>
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Discount Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" id="discount-name" value="{{ old('name') }}" placeholder="e.g. Weekend Flash Sale, Ramadan Special" class="soma-input" required autofocus>
                                <p class="text-[11px] text-slate-400 mt-1">Descriptive title shown to staff & receipts.</p>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-semibold text-slate-700">
                                        Promo / Coupon Code
                                    </label>
                                    <button type="button" onclick="generateCode()" class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                        <i class="fas fa-magic text-[10px]"></i> Auto-generate
                                    </button>
                                </div>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                        <i class="fas fa-ticket-alt text-xs"></i>
                                    </div>
                                    <input type="text" name="code" id="discount-code" value="{{ old('code') }}" placeholder="e.g. FLASH20, SAVE500 (Leave blank for automatic)" class="soma-input pl-9 uppercase tracking-wider font-mono">
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">If provided, cashiers or customers can enter this code at checkout.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Description / Internal Notes
                                </label>
                                <textarea name="description" id="discount-desc" rows="2" placeholder="Optional notes about terms, eligibility, or purpose..." class="soma-input">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Discount Value & Type Card -->
                    <div class="soma-card p-6">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
                            <i class="fas fa-calculator text-indigo-500"></i>
                            <span>Discount Configuration</span>
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2">
                                    Discount Type <span class="text-rose-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all border-blue-600 bg-blue-50/40" id="type-percentage-label">
                                        <input type="radio" name="type" value="percentage" {{ old('type', 'percentage') === 'percentage' ? 'checked' : '' }} onchange="updateType('percentage')" class="text-blue-600 focus:ring-blue-500">
                                        <div>
                                            <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                                <i class="fas fa-percent text-blue-600"></i> Percentage (%)
                                            </div>
                                            <div class="text-[11px] text-slate-500 mt-0.5">Deduct percentage of subtotal</div>
                                        </div>
                                    </label>
                                    <label class="relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all border-slate-200 hover:border-slate-300" id="type-fixed-label">
                                        <input type="radio" name="type" value="fixed" {{ old('type') === 'fixed' ? 'checked' : '' }} onchange="updateType('fixed')" class="text-blue-600 focus:ring-blue-500">
                                        <div>
                                            <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                                <i class="fas fa-money-bill-wave text-emerald-600"></i> Fixed Amount (KES)
                                            </div>
                                            <div class="text-[11px] text-slate-500 mt-0.5">Deduct specific shilling amount</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Discount Value <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 font-medium text-xs" id="value-symbol">
                                            %
                                        </div>
                                        <input type="number" step="0.01" min="0.01" name="value" id="discount-value" value="{{ old('value') }}" placeholder="0.00" class="soma-input pl-9" required>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1" id="value-help">e.g. 15 for 15% discount</p>
                                </div>

                                <div id="max-discount-wrapper">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Maximum Discount Cap (KES)
                                    </label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 font-medium text-xs">
                                            KES
                                        </div>
                                        <input type="number" step="0.01" min="0" name="max_discount" id="max-discount" value="{{ old('max_discount') }}" placeholder="No limit" class="soma-input pl-12">
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1">Upper limit for percentage discounts.</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Minimum Spend Requirement (KES)
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 font-medium text-xs">
                                        KES
                                    </div>
                                    <input type="number" step="0.01" min="0" name="min_spend" id="min-spend" value="{{ old('min_spend') }}" placeholder="0.00 (No minimum)" class="soma-input pl-12">
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Minimum subtotal required before discount applies.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Limits & Validity Card -->
                    <div class="soma-card p-6">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-amber-500"></i>
                            <span>Usage & Schedule</span>
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Total Redemptions Limit
                                </label>
                                <input type="number" min="1" step="1" name="usage_limit" id="usage-limit" value="{{ old('usage_limit') }}" placeholder="Unlimited" class="soma-input">
                                <p class="text-[11px] text-slate-400 mt-1">Maximum times this discount can be used in total.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Start Date & Time
                                    </label>
                                    <input type="datetime-local" name="starts_at" id="starts-at" value="{{ old('starts_at') }}" class="soma-input">
                                    <p class="text-[11px] text-slate-400 mt-1">Leave empty to activate immediately.</p>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        End Date & Time (Expiry)
                                    </label>
                                    <input type="datetime-local" name="ends_at" id="ends-at" value="{{ old('ends_at') }}" class="soma-input">
                                    <p class="text-[11px] text-slate-400 mt-1">Leave empty for no expiration date.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right 1 Col: Preview & Actions -->
                <div class="space-y-6">

                    <!-- Status Switcher Card -->
                    <div class="soma-card p-5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Status</h3>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                            <div>
                                <div class="text-xs font-semibold text-slate-900">Active upon creation</div>
                                <div class="text-[11px] text-slate-500">Uncheck to save as draft / disabled</div>
                            </div>
                        </label>
                    </div>

                    <!-- Live Summary Preview Card -->
                    <div class="soma-card p-5 bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between text-xs text-slate-400 mb-3 border-b border-slate-700/60 pb-2.5">
                            <span class="uppercase tracking-wider font-semibold">Live Preview</span>
                            <i class="fas fa-eye text-blue-400"></i>
                        </div>

                        <div class="space-y-3">
                            <div id="preview-badge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500 text-white shadow-sm">
                                <span id="preview-val">10% OFF</span>
                            </div>

                            <div class="text-lg font-bold text-white tracking-tight" id="preview-name">
                                Untitled Promotion
                            </div>

                            <div class="bg-slate-800/80 rounded-xl p-3 border border-slate-700 space-y-1 text-xs">
                                <div class="flex justify-between text-slate-300">
                                    <span>Code:</span>
                                    <span class="font-mono font-bold text-amber-400" id="preview-code">AUTOMATIC</span>
                                </div>
                                <div class="flex justify-between text-slate-300">
                                    <span>Min Spend:</span>
                                    <span id="preview-min">None</span>
                                </div>
                                <div class="flex justify-between text-slate-300">
                                    <span>Max Cap:</span>
                                    <span id="preview-cap">None</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Action Buttons -->
                    <div class="soma-card p-5 space-y-3">
                        <button type="submit" class="soma-btn-primary w-full justify-center py-2.5 font-semibold">
                            <i class="fas fa-save text-xs"></i> Save Discount
                        </button>
                        <a href="{{ route('discounts.index') }}" class="soma-btn-secondary w-full justify-center py-2.5 text-slate-600 hover:text-slate-800">
                            Cancel
                        </a>
                    </div>

                </div>

            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
let currentType = '{{ old('type', 'percentage') }}';

function updateType(type) {
    currentType = type;
    const pLabel = document.getElementById('type-percentage-label');
    const fLabel = document.getElementById('type-fixed-label');
    const symbol = document.getElementById('value-symbol');
    const help = document.getElementById('value-help');
    const maxWrapper = document.getElementById('max-discount-wrapper');

    if (type === 'percentage') {
        pLabel.className = 'relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all border-blue-600 bg-blue-50/40';
        fLabel.className = 'relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all border-slate-200 hover:border-slate-300';
        symbol.textContent = '%';
        help.textContent = 'e.g. 15 for 15% discount';
        maxWrapper.style.display = 'block';
    } else {
        fLabel.className = 'relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all border-blue-600 bg-blue-50/40';
        pLabel.className = 'relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all border-slate-200 hover:border-slate-300';
        symbol.textContent = 'KES';
        help.textContent = 'e.g. 500 for KES 500 discount';
        maxWrapper.style.display = 'none';
    }
    updatePreview();
}

function generateCode() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let code = 'SAVE';
    for (let i = 0; i < 4; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('discount-code').value = code;
    updatePreview();
}

function updatePreview() {
    const name = document.getElementById('discount-name').value;
    const code = document.getElementById('discount-code').value;
    const val = document.getElementById('discount-value').value;
    const min = document.getElementById('min-spend').value;
    const cap = document.getElementById('max-discount').value;

    document.getElementById('preview-name').textContent = name ? name : 'Untitled Promotion';
    document.getElementById('preview-code').textContent = code ? code.toUpperCase() : 'AUTOMATIC';
    
    if (currentType === 'percentage') {
        document.getElementById('preview-val').textContent = (val ? val : '0') + '% OFF';
        document.getElementById('preview-badge').className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500 text-white shadow-sm';
    } else {
        document.getElementById('preview-val').textContent = 'KES ' + (val ? Number(val).toLocaleString() : '0') + ' OFF';
        document.getElementById('preview-badge').className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-sm';
    }

    document.getElementById('preview-min').textContent = min ? 'KES ' + Number(min).toLocaleString() : 'None';
    document.getElementById('preview-cap').textContent = (currentType === 'percentage' && cap) ? 'KES ' + Number(cap).toLocaleString() : 'None';
}

document.getElementById('discount-name').addEventListener('input', updatePreview);
document.getElementById('discount-code').addEventListener('input', updatePreview);
document.getElementById('discount-value').addEventListener('input', updatePreview);
document.getElementById('min-spend').addEventListener('input', updatePreview);
document.getElementById('max-discount').addEventListener('input', updatePreview);

// Initial call
updateType(currentType);
updatePreview();
</script>
@endpush
@endsection
