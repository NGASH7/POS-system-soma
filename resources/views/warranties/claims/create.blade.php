@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 720px; margin: 0 auto; padding: 24px 16px;">

    <!-- Header -->
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:20px; flex-wrap:wrap;">
        <div>
            <h1 class="text-2xl font-bold text-gray-900" style="display:flex; align-items:center;">
                <span style="width:36px;height:36px;border-radius:10px;background:#fef9c3;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                    <i class="fas fa-ticket-alt" style="color:#ca8a04;"></i>
                </span>
                File Claim Ticket
            </h1>
            <p class="text-gray-500 text-sm" style="margin-top:4px; margin-left:48px;">
                Open a new warranty claim ticket for
                <span class="font-mono font-semibold text-gray-700">{{ $warranty->warranty_no }}</span>
            </p>
        </div>
        <a href="{{ route('warranties.show', $warranty) }}"
           class="text-sm"
           style="background:#f3f4f6;color:#374151;padding:10px 16px;border-radius:8px;display:inline-flex;align-items:center;white-space:nowrap;text-decoration:none;">
            <i class="fas fa-arrow-left" style="margin-right:8px;"></i>Back to Warranty
        </a>
    </div>

    @if ($errors->any())
        <div class="rounded-r" style="background:#fef2f2;border-left:4px solid #ef4444;padding:16px;margin-bottom:24px;">
            <div style="display:flex;">
                <i class="fas fa-exclamation-circle" style="color:#ef4444;margin-top:2px;"></i>
                <div style="margin-left:12px;">
                    <h3 class="text-sm font-medium" style="color:#991b1b;">Please correct the following:</h3>
                    <ul class="text-sm" style="color:#b91c1c;margin-top:8px;list-style:disc;padding-left:18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Warranty Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200" style="padding:20px; margin-bottom:24px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <span class="text-xs font-semibold text-gray-500 uppercase" style="letter-spacing:0.05em;">Warranty Summary</span>
            <span class="text-xs font-medium {{ $warranty->status_badge }}" style="padding:4px 10px;border-radius:9999px;">
                {{ ucfirst($warranty->status) }}
            </span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="text-gray-500 text-xs" style="display:block;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px;">Warranty #</span>
                <span class="font-mono font-bold text-gray-900">{{ $warranty->warranty_no }}</span>
            </div>
            <div>
                <span class="text-gray-500 text-xs" style="display:block;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px;">Product</span>
                <span class="font-semibold text-gray-900">{{ $warranty->product->name ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-gray-500 text-xs" style="display:block;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px;">Customer</span>
                <span class="font-semibold text-gray-900">{{ $warranty->customer->name ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-gray-500 text-xs" style="display:block;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px;">Serial #</span>
                <span class="font-mono text-gray-900">{{ $warranty->serial_number ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('warranties.claims.store', $warranty) }}" method="POST"
          class="bg-white rounded-xl shadow-sm border border-gray-200" style="padding:24px;" id="claimForm">
        @csrf

        <div style="margin-bottom:24px;">
            <label class="text-sm font-medium text-gray-700" style="display:block;margin-bottom:6px;">
                Claim Date <span style="color:#ef4444;">*</span>
            </label>
            <input type="date" name="claim_date" value="{{ old('claim_date', date('Y-m-d')) }}" required
                   class="border border-gray-300 rounded-lg"
                   style="width:100%;padding:10px 12px;">
        </div>

        <div style="margin-bottom:24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <label class="text-sm font-medium text-gray-700">
                    Issue Description / Defect Details <span style="color:#ef4444;">*</span>
                </label>
                <span class="text-xs text-gray-400" id="charCount">0 characters</span>
            </div>
            <textarea name="issue_description" rows="5" required
                      placeholder="Describe the product malfunction, error, hardware fault, or customer complaint in detail..."
                      id="issueDescription"
                      class="border border-gray-300 rounded-lg"
                      style="width:100%;padding:10px 12px;resize:vertical;">{{ old('issue_description') }}</textarea>
            <p class="text-xs text-gray-400" style="margin-top:6px;">Tip: include when the issue started and any troubleshooting already tried — it speeds up approval.</p>
        </div>

        <!-- Action row: table-based layout so it can never collapse, wrap-off-screen, or get clipped -->
        <div style="border-top:1px solid #e5e7eb; padding-top:20px; display:flex; flex-wrap:wrap; justify-content:flex-end; gap:12px;">
            <a href="{{ route('warranties.show', $warranty) }}"
               class="border border-gray-300 text-gray-700 rounded-lg"
               style="padding:10px 20px; text-decoration:none; text-align:center; display:inline-block;">
                Cancel
            </a>
            <button type="submit" id="submitBtn"
                    style="background-color:#ca8a04; color:#ffffff; padding:10px 24px; border-radius:8px; font-weight:600; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; min-width:180px;">
                <i class="fas fa-paper-plane" id="submitIcon" style="margin-right:8px;"></i>
                <span id="submitLabel">Submit Claim Ticket</span>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var textarea = document.getElementById('issueDescription');
    var charCount = document.getElementById('charCount');
    var form = document.getElementById('claimForm');
    var submitBtn = document.getElementById('submitBtn');
    var submitIcon = document.getElementById('submitIcon');
    var submitLabel = document.getElementById('submitLabel');

    function updateCharCount() {
        var len = textarea.value.length;
        charCount.textContent = len + ' character' + (len === 1 ? '' : 's');
        charCount.style.color = (len > 0 && len < 20) ? '#ef4444' : '#9ca3af';
    }
    updateCharCount();
    textarea.addEventListener('input', updateCharCount);

    // Prevent double submission and show a submitting state
    form.addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.6';
        submitBtn.style.cursor = 'not-allowed';
        submitIcon.classList.remove('fa-paper-plane');
        submitIcon.classList.add('fa-spinner', 'fa-spin');
        submitLabel.textContent = 'Submitting...';
    });

    submitBtn.addEventListener('mouseenter', function () {
        if (!submitBtn.disabled) submitBtn.style.backgroundColor = '#a16207';
    });
    submitBtn.addEventListener('mouseleave', function () {
        if (!submitBtn.disabled) submitBtn.style.backgroundColor = '#ca8a04';
    });
});
</script>
@endsection