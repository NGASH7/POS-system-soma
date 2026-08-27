@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-950">Profile Settings</h1>
        <p class="text-slate-500 mt-2">Manage your account settings and preferences</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar / Profile Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-center">
                    <div class="relative inline-block">
                        <!-- Avatar / Profile Photo -->
                        <div class="w-28 h-28 bg-white rounded-full flex items-center justify-center mx-auto shadow-lg overflow-hidden border-4 border-white">
                            @if(Auth::user()->profile_photo)
                                <img src="{{ Storage::url(Auth::user()->profile_photo) }}" 
                                     alt="{{ Auth::user()->name }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <span class="text-5xl font-bold text-blue-700">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </span>
                            @endif
                        </div>

                        <!-- Hidden File Input -->
                        <input type="file" name="profile_photo" id="profile_photo_input" 
                               accept="image/*" class="hidden">

                        <!-- Camera Button -->
                        <button type="button" onclick="document.getElementById('profile_photo_input').click();" 
                                class="absolute bottom-0 right-0 bg-white rounded-full p-2 shadow-md hover:bg-slate-50 transition cursor-pointer">
                            <i class="fas fa-camera text-blue-700 text-sm"></i>
                        </button>
                    </div>
                    <h3 class="text-white text-xl font-bold mt-4">{{ Auth::user()->name }}</h3>
                    <p class="text-blue-200 text-sm">{{ Auth::user()->email }}</p>
                    <p class="text-blue-200 text-xs mt-2">Member since {{ Auth::user()->created_at->format('M d, Y') }}</p>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3 text-slate-500">
                            <i class="fas fa-calendar-alt w-5 text-blue-600"></i>
                            <span class="text-sm">Joined: {{ Auth::user()->created_at->format('F d, Y') }}</span>
                        </div>
                        <div class="flex items-center space-x-3 text-slate-500">
                            <i class="fas fa-clock w-5 text-blue-600"></i>
                            <span class="text-sm">Last updated: {{ Auth::user()->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="border-t border-slate-200 my-4"></div>
                        <div class="flex items-center space-x-3 text-slate-500">
                            <i class="fas fa-shield-alt w-5 text-green-500"></i>
                            <span class="text-sm">Account Status: <span class="text-green-600 font-semibold">Active</span></span>
                        </div>
                        <div class="flex items-center space-x-3 text-slate-500">
                            <i class="fas fa-credit-card w-5 text-blue-600"></i>
                            <span class="text-sm">Role: <span class="font-semibold">Administrator</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Update Profile Form (Text Only) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-lg font-semibold text-slate-900">
                        <i class="fas fa-user-edit text-blue-700 mr-2"></i>
                        Personal Information
                    </h2>
                </div>
                
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                        @csrf
                        @method('patch')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    <i class="fas fa-user mr-1 text-blue-600"></i> Full Name
                                </label>
                                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" 
                                       class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                       required>
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    <i class="fas fa-envelope mr-1 text-blue-600"></i> Email Address
                                </label>
                                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                                       class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                       required>
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded-lg font-semibold transition transform hover:scale-105">
                                <i class="fas fa-save mr-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Update Password Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-lg font-semibold text-slate-900">
                        <i class="fas fa-lock text-blue-700 mr-2"></i>
                        Change Password
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Update your password to keep your account secure</p>
                </div>
                
                <div class="p-6">
                    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                        @csrf
                        @method('put')
                        
                        <!-- Current Password -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                <i class="fas fa-key mr-1 text-blue-600"></i> Current Password
                            </label>
                            <input type="password" name="current_password" 
                                   class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Enter your current password">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- New Password -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    <i class="fas fa-lock mr-1 text-blue-600"></i> New Password
                                </label>
                                <input type="password" name="password" 
                                       class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                       placeholder="Enter new password">
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Confirm Password -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    <i class="fas fa-check-circle mr-1 text-blue-600"></i> Confirm Password
                                </label>
                                <input type="password" name="password_confirmation" 
                                       class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                       placeholder="Confirm new password">
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded-lg font-semibold transition transform hover:scale-105">
                                <i class="fas fa-key mr-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Session Management -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-lg font-semibold text-slate-900">
                        <i class="fas fa-desktop text-blue-700 mr-2"></i>
                        Active Sessions
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Manage your active sessions across devices</p>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-laptop text-blue-700"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900">Current Session</p>
                                    <p class="text-xs text-slate-500">{{ request()->ip() }} • {{ request()->userAgent() }}</p>
                                </div>
                            </div>
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Active Now</span>
                        </div>
                        
                        <button onclick="showNotification('Session management coming soon!', 'info')" 
                                class="text-red-600 hover:text-red-700 text-sm font-medium flex items-center space-x-2">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout Other Sessions</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 rounded-2xl border border-red-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-red-200 bg-red-100">
                    <h2 class="text-lg font-semibold text-red-800">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                        Danger Zone
                    </h2>
                    <p class="text-sm text-red-700 mt-1">Permanently delete your account</p>
                </div>
                
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-800 font-medium">Delete Account</p>
                            <p class="text-sm text-red-600">Once deleted, all your data will be permanently removed.</p>
                        </div>
                        <button onclick="confirmDelete()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                            <i class="fas fa-trash-alt mr-2"></i>Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
function confirmDelete() {
    if (confirm('⚠️ WARNING: This action cannot be undone!\n\nAre you sure you want to delete your account?\nAll your data will be permanently removed.')) {
        document.getElementById('delete-account-form').submit();
    }
}

// ULTIMATE PHOTO UPLOAD FIX (Using XMLHttpRequest for 100% reliability)
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('profile_photo_input');
    const cameraIcon = document.querySelector('.fa-camera');
    
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                
                // 1. Get the CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    alert('CSRF token missing! Please refresh the page.');
                    return;
                }

                // 2. Prepare the data
                let formData = new FormData();
                formData.append('profile_photo', this.files[0]);
                formData.append('_token', csrfToken);
                formData.append('_method', 'PATCH'); // This MUST match your route

                // 3. Show a spinning loading wheel instead of the camera
                if(cameraIcon) {
                    cameraIcon.className = 'fas fa-spinner fa-spin text-blue-700 text-sm';
                }

                // 4. Send the request using XMLHttpRequest
                let xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('profile.update') }}', true);

                xhr.onload = function() {
                    // Reset the icon back to camera
                    if(cameraIcon) {
                        cameraIcon.className = 'fas fa-camera text-blue-700 text-sm';
                    }

                    if (xhr.status === 200 || xhr.status === 302) {
                        // SUCCESS: Reload the page to show the new picture!
                        window.location.reload();
                    } else {
                        // ERROR: Show exactly what Laravel said
                        let errorMsg = 'Unknown server error (Status ' + xhr.status + ')';
                        try {
                            let response = JSON.parse(xhr.responseText);
                            if (response.errors && response.errors.profile_photo) {
                                errorMsg = response.errors.profile_photo[0];
                            } else if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch(e) {}
                        
                        alert('Upload failed: ' + errorMsg);
                    }
                };

                xhr.onerror = function() {
                    // Reset the icon back to camera
                    if(cameraIcon) {
                        cameraIcon.className = 'fas fa-camera text-blue-700 text-sm';
                    }
                    alert('Network error. Please check your connection.');
                };

                xhr.send(formData);
            }
        });
    }
});
</script>

<!-- Delete Account Form -->
<form method="POST" action="{{ route('profile.destroy') }}" id="delete-account-form" class="hidden">
    @csrf
    @method('delete')
</form>
@endsection

@push('styles')
<style>
    .transform {
        transition: transform 0.2s ease;
    }
    
    .hover\:scale-105:hover {
        transform: scale(1.05);
    }
</style>
@endpush