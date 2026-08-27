@extends('layouts.app')

@section('content')
<div class="container-fluid max-w-6xl mx-auto py-6">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $warranty->warranty_no }}
                </h1>
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $warranty->status_badge }}">
                    {{ ucfirst($warranty->status) }}
                </span>
            </div>
            <p class="text-gray-500 text-sm mt-1">Registered on {{ $warranty->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('warranties.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <a href="{{ route('warranties.print', $warranty) }}" target="_blank" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-print mr-2"></i>Print Certificate
            </a>
            <a href="{{ route('warranties.edit', $warranty) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            @if($warranty->claims->count() == 0)
            <a href="{{ route('warranties.claims.create', $warranty) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-ticket-alt mr-2"></i>New Claim Ticket
            </a>
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Details Card -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
            <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>Warranty Specifications
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500 block">Warranty Type</span>
                    <span class="font-semibold text-gray-900">{{ $warranty->type_label }}</span>
                </div>

                <div>
                    <span class="text-gray-500 block">Duration</span>
                    <span class="font-semibold text-gray-900">{{ $warranty->warranty_duration_months }} Months</span>
                </div>

                <div>
                    <span class="text-gray-500 block">Purchase Date</span>
                    <span class="font-semibold text-gray-900">{{ $warranty->purchase_date ? $warranty->purchase_date->format('d/m/Y') : 'N/A' }}</span>
                </div>

                <div>
                    <span class="text-gray-500 block">Expiry Date</span>
                    <span class="font-semibold {{ $warranty->expiry_date && $warranty->expiry_date->isPast() ? 'text-red-600' : 'text-green-600' }}">
                        {{ $warranty->expiry_date ? $warranty->expiry_date->format('d/m/Y') : 'N/A' }}
                        @if($warranty->expiry_date && $warranty->expiry_date->isPast())
                            (Expired)
                        @endif
                    </span>
                </div>

                <div>
                    <span class="text-gray-500 block">Serial Number</span>
                    <span class="font-mono font-semibold text-gray-900">{{ $warranty->serial_number ?? 'N/A' }}</span>
                </div>

                <div>
                    <span class="text-gray-500 block">Batch / Lot Number</span>
                    <span class="font-mono font-semibold text-gray-900">{{ $warranty->batch_number ?? 'N/A' }}</span>
                </div>
            </div>

            @if($warranty->terms)
                <div class="border-t border-gray-100 pt-4">
                    <span class="text-gray-500 text-sm block font-medium mb-1">Terms & Conditions</span>
                    <div class="bg-gray-50 rounded-lg p-3 text-sm text-gray-700 whitespace-pre-line">{{ $warranty->terms }}</div>
                </div>
            @endif

            @if($warranty->notes)
                <div class="border-t border-gray-100 pt-4">
                    <span class="text-gray-500 text-sm block font-medium mb-1">Internal Notes</span>
                    <div class="bg-yellow-50 rounded-lg p-3 text-sm text-gray-700 whitespace-pre-line border border-yellow-100">{{ $warranty->notes }}</div>
                </div>
            @endif
        </div>

        <!-- Product & Customer Cards -->
        <div class="space-y-6">
            <!-- Product Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-md font-bold text-gray-900 border-b border-gray-100 pb-2 mb-3">
                    <i class="fas fa-box text-blue-600 mr-2"></i>Product Details
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="font-semibold text-base text-gray-900">{{ $warranty->product->name ?? 'N/A' }}</div>
                    <div class="text-gray-500">SKU: <span class="font-mono text-gray-800">{{ $warranty->product->sku ?? 'N/A' }}</span></div>
                    @if(isset($warranty->product->price))
                        <div class="text-gray-500">Price: <span class="font-semibold text-gray-800">KES {{ number_format($warranty->product->price, 2) }}</span></div>
                    @endif
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-md font-bold text-gray-900 border-b border-gray-100 pb-2 mb-3">
                    <i class="fas fa-user text-blue-600 mr-2"></i>Customer Details
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="font-semibold text-base text-gray-900">{{ $warranty->customer->name ?? 'N/A' }}</div>
                    <div class="text-gray-500"><i class="fas fa-phone text-gray-400 mr-1"></i> {{ $warranty->customer->phone ?? 'N/A' }}</div>
                    <div class="text-gray-500"><i class="fas fa-envelope text-gray-400 mr-1"></i> {{ $warranty->customer->email ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Sale Info -->
            @if($warranty->sale)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-md font-bold text-gray-900 border-b border-gray-100 pb-2 mb-3">
                        <i class="fas fa-shopping-cart text-blue-600 mr-2"></i>Associated Sale
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="font-mono font-semibold text-gray-900">{{ $warranty->sale->invoice_number ?? 'Invoice #'.$warranty->sale->id }}</div>
                        <div class="text-gray-500">Date: {{ $warranty->sale->created_at ? $warranty->sale->created_at->format('d/m/Y') : '' }}</div>
                        <div class="text-gray-500">Total Amount: <span class="font-semibold text-gray-800">KES {{ number_format($warranty->sale->grand_total ?? 0, 2) }}</span></div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Warranty Claims / Tickets Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-ticket-alt text-yellow-600 mr-2"></i>Warranty Claim Tickets ({{ $warranty->claims->count() }})
                </h2>
                <p class="text-gray-500 text-sm">History of tickets filed under this warranty</p>
            </div>
            @if($warranty->claims->count() == 0)
            <a href="{{ route('warranties.claims.create', $warranty) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-plus mr-2"></i>File New Ticket
            </a>
            @else
            <span class="inline-flex items-center gap-2 bg-amber-50 border border-amber-300 text-amber-700 text-xs font-semibold px-3 py-2 rounded-lg">
                <i class="fas fa-lock"></i> Claim Already Filed
            </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3">Ticket #</th>
                        <th class="px-4 py-3">Claim Date</th>
                        <th class="px-4 py-3">Issue Description</th>
                        <th class="px-4 py-3">Resolution</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($warranty->claims as $claim)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono font-bold text-gray-900">{{ $claim->claim_no }}</td>
                            <td class="px-4 py-3">{{ $claim->claim_date ? $claim->claim_date->format('d/m/Y') : '' }}</td>
                            <td class="px-4 py-3 max-w-xs truncate">{{ $claim->issue_description }}</td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-800">{{ $claim->resolution_label }}</span>
                                @if($claim->resolution_notes)
                                    <span class="block text-xs text-gray-500 truncate" title="{{ $claim->resolution_notes }}">{{ $claim->resolution_notes }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full {{ $claim->status_badge }}">
                                    {{ ucfirst($claim->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="openClaimModal({{ json_encode($claim) }})" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1 rounded text-xs font-medium transition">
                                    <i class="fas fa-edit mr-1"></i> Update Ticket
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                <i class="fas fa-folder-open text-3xl mb-2 block"></i>
                                No claim tickets have been filed for this warranty.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Claim Ticket Update Modal -->
<div id="claimModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
        <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
            <h3 class="text-lg font-bold text-gray-900" id="modalTitle">Update Claim Ticket</h3>
            <button onclick="closeClaimModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form id="claimForm" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="claimStatus" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Resolution</label>
                    <select name="resolution" id="claimResolution" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="none">None</option>
                        <option value="repair">Repair</option>
                        <option value="replace">Replace</option>
                        <option value="refund">Refund</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Resolution Notes</label>
                    <textarea name="resolution_notes" id="claimNotes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Details of repair, replacement part, or decision..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Resolved Date</label>
                    <input type="date" name="resolved_date" id="claimResolvedDate" value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
            </div>

            <div class="flex justify-end space-x-3 border-t border-gray-100 pt-4">
                <button type="button" onclick="closeClaimModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openClaimModal(claim) {
    document.getElementById('modalTitle').innerText = 'Update Claim Ticket: ' + claim.claim_no;
    document.getElementById('claimForm').action = '/claims/' + claim.id;
    document.getElementById('claimStatus').value = claim.status || 'pending';
    document.getElementById('claimResolution').value = claim.resolution || 'none';
    document.getElementById('claimNotes').value = claim.resolution_notes || '';
    if (claim.resolved_date) {
        document.getElementById('claimResolvedDate').value = claim.resolved_date.substring(0, 10);
    }
    document.getElementById('claimModal').classList.remove('hidden');
}

function closeClaimModal() {
    document.getElementById('claimModal').classList.add('hidden');
}
</script>
@endsection
