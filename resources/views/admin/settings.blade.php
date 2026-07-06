@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-950">System Settings</h1>
        <p class="text-slate-500 mt-2">Configure system preferences</p>
    </div>

    <div class="soma-card p-6">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            
            <div class="space-y-6">
                <div class="border-b border-slate-200 pb-4">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">General Settings</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">System Name</label>
                        <input type="text" name="system_name" value="{{ config('app.name', 'Soma POS') }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tax Rate (%)</label>
                        <input type="number" name="tax_rate" value="16" step="0.01" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                    </div>
                </div>
                
                <div class="border-b border-slate-200 pb-4">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Receipt Settings</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Store Name</label>
                        <input type="text" name="store_name" value="Soma POS Store" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Store Address</label>
                        <textarea name="store_address" rows="2" class="w-full px-4 py-2 border border-slate-200 rounded-lg">123 Main Street, Nairobi, Kenya</textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Store Phone</label>
                        <input type="text" name="store_phone" value="+254 712 345 678" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Notification Settings</h3>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="low_stock_notifications" checked class="mr-2">
                        <span class="text-sm text-slate-700">Enable low stock notifications</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end mt-6 pt-4 border-t border-slate-200">
                <button type="submit" class="px-6 py-2 bg-blue-700 text-white rounded-xl hover:bg-blue-800 transition">
                    <i class="fas fa-save mr-2"></i>Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
