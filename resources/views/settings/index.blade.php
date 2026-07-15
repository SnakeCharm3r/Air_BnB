@extends('layouts.app')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-bold text-slate-900">System Settings</h1>
        <p class="text-sm text-slate-500 mt-1">Manage lodge configuration and system preferences</p>
    </div>

    {{-- Success/error messages handled by global toast notifications --}}

    <!-- Main Layout: Settings Form (Left) + Sidebar (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Left: Settings Form (3 columns) -->
        <form id="settings-form" action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="lg:col-span-3 space-y-6">
            @csrf
            @method('PUT')

            <!-- Lodge Information Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        @include('components.icons.rooms', ['class' => 'w-5 h-5 text-amber-600'])
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-800">Lodge Information</h3>
                        <p class="text-xs text-slate-500">General lodge details and branding</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Lodge Logo -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Lodge Logo / Icon</label>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center border border-dashed border-slate-300 flex-shrink-0">
                                @if($settings->lodge_logo)
                                    <img src="{{ asset('storage/' . $settings->lodge_logo) }}" class="w-10 h-10 object-contain">
                                @else
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="lodge_logo" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                                <p class="text-xs text-slate-400 mt-1">128x128px PNG or SVG</p>
                            </div>
                        </div>
                    </div>

                    <!-- Lodge Name -->
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Lodge Name</label>
                        <input type="text" name="lodge_name" value="{{ $settings->lodge_name }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <!-- Contact Email -->
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Contact Email</label>
                        <input type="email" name="contact_email" value="{{ $settings->contact_email }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <!-- Contact Phone -->
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Contact Phone</label>
                        <input type="text" name="contact_phone" value="{{ $settings->contact_phone }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <!-- Owner Notification Email -->
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Owner Notification Email</label>
                        <input type="email" name="owner_email" value="{{ $settings->owner_email }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <!-- Contact Address -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Contact Address</label>
                        <input type="text" name="contact_address" value="{{ $settings->contact_address }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <!-- Currency -->
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Currency</label>
                        <input type="text" name="currency" value="{{ $settings->currency ?? 'TSH' }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
            </div>

            <!-- Notifications & Security Side by Side -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Notifications Card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            @include('components.icons.notifications', ['class' => 'w-4 h-4 text-blue-600'])
                        </div>
                        <h3 class="text-sm font-semibold text-slate-800">Notifications</h3>
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-start gap-2 cursor-pointer">
                            <input type="checkbox" name="checkout_reminders" value="1" {{ ($settings->notification_settings['checkout_reminders'] ?? true) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                            <div>
                                <p class="text-xs font-medium text-slate-700">Checkout reminders</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-2 cursor-pointer">
                            <input type="checkbox" name="low_inventory_alerts" value="1" {{ ($settings->notification_settings['low_inventory_alerts'] ?? true) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                            <div>
                                <p class="text-xs font-medium text-slate-700">Low inventory alerts</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-2 cursor-pointer">
                            <input type="checkbox" name="maintenance_due" value="1" {{ ($settings->notification_settings['maintenance_due'] ?? true) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                            <div>
                                <p class="text-xs font-medium text-slate-700">Maintenance due</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-2 cursor-pointer">
                            <input type="checkbox" name="device_status_changes" value="1" {{ ($settings->notification_settings['device_status_changes'] ?? true) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                            <div>
                                <p class="text-xs font-medium text-slate-700">Device status changes</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Security Card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-slate-800">Security</h3>
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-xs text-slate-600">Two-Factor Auth</span>
                            <input type="checkbox" name="two_factor_auth" value="1" {{ $settings->two_factor_auth ? 'checked' : '' }} class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                        </label>
                        <div class="flex items-center justify-between gap-2">
                            <label class="text-xs text-slate-600" for="session_timeout">Session Timeout (hrs)</label>
                            <input type="number" id="session_timeout" name="session_timeout" value="{{ $settings->session_timeout }}" min="1" max="168" class="w-16 px-2 py-1 text-xs text-right border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <label class="text-xs text-slate-600" for="max_login_attempts">Max Login Attempts</label>
                            <input type="number" id="max_login_attempts" name="max_login_attempts" value="{{ $settings->max_login_attempts }}" min="1" max="10" class="w-16 px-2 py-1 text-xs text-right border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <label class="text-xs text-slate-600" for="lockout_duration">Lockout (min)</label>
                            <input type="number" id="lockout_duration" name="lockout_duration" value="{{ $settings->lockout_duration }}" min="1" max="1440" class="w-16 px-2 py-1 text-xs text-right border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-xs text-slate-600">Audit Logging</span>
                            <input type="checkbox" name="audit_logging" value="1" {{ $settings->audit_logging ? 'checked' : '' }} class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                        </label>
                    </div>
                </div>
            </div>

            <!-- Branding Assets -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-800">Branding Assets</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Login Logo</label>
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center border border-dashed border-slate-300 flex-shrink-0">
                                @if($settings->login_logo)
                                    <img src="{{ asset('storage/' . $settings->login_logo) }}" class="w-8 h-8 object-contain">
                                @else
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>
                            <input type="file" name="login_logo" accept="image/*" class="text-xs text-slate-500 file:mr-1 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Favicon</label>
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center border border-dashed border-slate-300 flex-shrink-0">
                                @if($settings->favicon)
                                    <img src="{{ asset('storage/' . $settings->favicon) }}" class="w-8 h-8 object-contain">
                                @else
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>
                            <input type="file" name="favicon" accept="image/png,image/x-icon" class="text-xs text-slate-500 file:mr-1 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Profile Photo</label>
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                                @if(auth()->user()->profile_photo)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <span class="text-amber-600 font-bold text-xs">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                                @endif
                            </div>
                            <a href="{{ route('profile') }}" class="text-xs text-amber-600 hover:text-amber-700 font-medium">Change</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Right: Sidebar (1 column) -->
        <div class="space-y-6">
            <!-- Save Changes Button -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-semibold text-slate-800 mb-4">Actions</h3>
                <div class="space-y-3">
                    <button type="submit" form="settings-form" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                    <button type="button" onclick="document.getElementById('settings-form').reset()" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </button>
                </div>
            </div>

            <!-- System Info -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-800">System Info</h3>
                </div>

                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-slate-500">Version</p>
                        <p class="text-sm font-medium text-slate-700">1.0.0</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Database</p>
                        <p class="text-sm font-medium text-emerald-600">Connected</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Your Role</p>
                        <p class="text-sm font-medium text-slate-700 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Last Update</p>
                        <p class="text-sm font-medium text-slate-700">{{ $settings->updated_at ? $settings->updated_at->format('M d, Y') : '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- User Activity -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center">
                        @include('components.icons.users', ['class' => 'w-4 h-4 text-rose-600'])
                    </div>
                    <h3 class="text-sm font-semibold text-slate-800">User Activity</h3>
                </div>

                <div class="grid grid-cols-3 gap-2 mb-3">
                    <div class="text-center p-2 bg-slate-50 rounded-lg">
                        <p class="text-lg font-bold text-emerald-600">{{ $activeUsers }}</p>
                        <p class="text-xs text-slate-500">Active</p>
                    </div>
                    <div class="text-center p-2 bg-slate-50 rounded-lg">
                        <p class="text-lg font-bold text-rose-600">{{ $lockedUsers }}</p>
                        <p class="text-xs text-slate-500">Locked</p>
                    </div>
                    <div class="text-center p-2 bg-slate-50 rounded-lg">
                        <p class="text-lg font-bold text-amber-600">{{ $failedAttempts }}</p>
                        <p class="text-xs text-slate-500">Failed</p>
                    </div>
                </div>

                @if($recentFailedLogins->count() > 0)
                    <div class="border-t border-slate-200 pt-3">
                        <h4 class="text-xs font-medium text-slate-700 mb-2">Recent Failed Logins</h4>
                        <div class="space-y-2 max-h-32 overflow-y-auto">
                            @foreach($recentFailedLogins as $user)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-600">{{ $user->name }}</span>
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs px-1.5 py-0.5 bg-rose-100 text-rose-700 rounded">{{ $user->failed_attempts }}</span>
                                        @if($user->status === 'locked')
                                            <form action="{{ route('users.unlock', $user) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Unlock</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
