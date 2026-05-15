@extends('layouts.app')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-xl font-bold text-slate-800">System Settings</h2>
        <p class="text-sm text-slate-500 mt-0.5">Manage lodge configuration and system preferences</p>
    </div>

    <!-- Lodge Information -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Lodge Information</h3>
                    <p class="text-xs text-slate-500">General lodge details and branding</p>
                </div>
            </div>
            <button onclick="toggleEditMode()" id="btn-edit-info" class="flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-medium transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Edit
            </button>
            <div id="btn-group-info" class="hidden flex gap-2">
                <button onclick="cancelEditInfo()" class="flex items-center gap-1.5 px-3 py-1.5 border border-slate-300 text-slate-600 rounded-lg text-xs font-medium hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button onclick="saveLodgeInfo()" class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-medium transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save
                </button>
            </div>
        </div>

        <!-- Display Mode -->
        <div id="lodge-display" class="space-y-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-slate-100 rounded-xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-sm text-slate-500">
                    No logo set
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="flex items-center gap-2 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span id="disp-email">No email set</span>
                </div>
                <div class="flex items-center gap-2 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span id="disp-phone">No phone set</span>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span id="disp-address">No address set</span>
            </div>
            <p class="text-xs text-slate-400">Owner notifications: Not configured</p>
        </div>

        <!-- Edit Mode -->
        <div id="lodge-edit" class="hidden space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Lodge Logo / Icon</label>
                <div class="flex items-center gap-3">
                    <div class="w-16 h-16 bg-slate-100 rounded-xl flex items-center justify-center border-2 border-dashed border-slate-300">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <input type="text" id="input-logo" placeholder="https://example.com/logo.png" 
                        class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <button class="p-2 text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-slate-400 mt-1">Paste a URL or upload an image</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Lodge Name</label>
                <input type="text" id="input-lodge-name" 
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Contact Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input type="email" id="input-lodge-email" 
                            class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Contact Phone</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </span>
                        <input type="tel" id="input-lodge-phone" 
                            class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Contact Address</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </span>
                    <textarea id="input-lodge-address" rows="2"
                        class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 resize-none"></textarea>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Owner Notification Email</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <input type="email" id="input-notify-email" 
                        class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                </div>
                <p class="text-xs text-slate-400 mt-1">Profile changes will notify this email</p>
            </div>
        </div>
    </div>

    <!-- Notifications & Security Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Notifications -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Notifications</h3>
                    <p class="text-xs text-slate-500">Alert and notification preferences</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" checked class="mt-0.5 w-4 h-4 text-amber-500 rounded border-slate-300 focus:ring-amber-400">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Checkout reminders</p>
                        <p class="text-xs text-slate-500">Alert when guests are due to check out</p>
                    </div>
                </label>
                
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" checked class="mt-0.5 w-4 h-4 text-amber-500 rounded border-slate-300 focus:ring-amber-400">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Low inventory alerts</p>
                        <p class="text-xs text-slate-500">Notify when stock falls below threshold</p>
                    </div>
                </label>
                
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" checked class="mt-0.5 w-4 h-4 text-amber-500 rounded border-slate-300 focus:ring-amber-400">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Maintenance due</p>
                        <p class="text-xs text-slate-500">Remind about scheduled maintenance</p>
                    </div>
                </label>
                
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" checked class="mt-0.5 w-4 h-4 text-amber-500 rounded border-slate-300 focus:ring-amber-400">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Device status changes</p>
                        <p class="text-xs text-slate-500">Alert when devices go offline</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Security -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Security</h3>
                    <p class="text-xs text-slate-500">Authentication and access control</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm text-slate-600">Two-Factor Auth</span>
                    <span class="text-sm text-slate-400">Not configured</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm text-slate-600">Session Timeout</span>
                    <span class="text-sm text-slate-800">24 hours</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm text-slate-600">Audit Logging</span>
                    <span class="text-sm text-emerald-600">Enabled</span>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800">System Info</h3>
                <p class="text-xs text-slate-500">Current system status</p>
            </div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-slate-500 mb-1">Version</p>
                <p class="font-medium text-slate-800">1.0.0</p>
            </div>
            <div>
                <p class="text-slate-500 mb-1">Database</p>
                <p class="font-medium text-emerald-600">Connected</p>
            </div>
            <div>
                <p class="text-slate-500 mb-1">Your Role</p>
                <p class="font-medium text-slate-800 capitalize" id="sys-role">Owner</p>
            </div>
            <div>
                <p class="text-slate-500 mb-1">Last Settings Update</p>
                <p class="font-medium text-slate-800">—</p>
            </div>
        </div>
    </div>
</div>

<script>
let originalLodgeData = {};

function toggleEditMode() {
    document.getElementById('lodge-display').classList.add('hidden');
    document.getElementById('lodge-edit').classList.remove('hidden');
    document.getElementById('btn-edit-info').classList.add('hidden');
    document.getElementById('btn-group-info').classList.remove('hidden');
    
    // Load current values into inputs
    const email = document.getElementById('disp-email').textContent;
    const phone = document.getElementById('disp-phone').textContent;
    const address = document.getElementById('disp-address').textContent;
    
    if (email !== 'No email set') document.getElementById('input-lodge-email').value = email;
    if (phone !== 'No phone set') document.getElementById('input-lodge-phone').value = phone;
    if (address !== 'No address set') document.getElementById('input-lodge-address').value = address;
}

function cancelEditInfo() {
    document.getElementById('lodge-display').classList.remove('hidden');
    document.getElementById('lodge-edit').classList.add('hidden');
    document.getElementById('btn-edit-info').classList.remove('hidden');
    document.getElementById('btn-group-info').classList.add('hidden');
}

function saveLodgeInfo() {
    const email = document.getElementById('input-lodge-email').value;
    const phone = document.getElementById('input-lodge-phone').value;
    const address = document.getElementById('input-lodge-address').value;
    
    // Update display
    document.getElementById('disp-email').textContent = email || 'No email set';
    document.getElementById('disp-phone').textContent = phone || 'No phone set';
    document.getElementById('disp-address').textContent = address || 'No address set';
    
    // Exit edit mode
    cancelEditInfo();
    
    // Show success (in real app, this would be an API call)
    alert('Lodge information saved successfully');
}

// Load user role on page load
document.addEventListener('DOMContentLoaded', function() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user.role) {
        document.getElementById('sys-role').textContent = user.role;
    }
});
</script>
@endsection
