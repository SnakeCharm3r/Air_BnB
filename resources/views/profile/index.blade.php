@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Profile Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <!-- Banner -->
        <div class="h-24 bg-gradient-to-r from-slate-800 to-slate-700 relative">
            <div class="absolute -bottom-8 left-6">
                <div class="w-16 h-16 rounded-xl border-4 border-white bg-amber-500 flex items-center justify-center shadow-lg">
                    <span class="text-white text-2xl font-bold">{{ strtoupper(substr($user->full_name ?? $user->name, 0, 1)) }}</span>
                </div>
            </div>
        </div>
        
        <!-- Profile Info -->
        <div class="pt-10 px-6 pb-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">{{ $user->full_name ?? $user->name }}</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 capitalize">
                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                            {{ $user->role }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                            Active
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Details & Password Side by Side -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Details Form -->
        <form id="profile-form" method="POST" action="{{ route('profile.update') }}" class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            @csrf
            @method('PUT')
            <h3 class="font-semibold text-slate-800 mb-4">Profile Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Full Name -->
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Full Name</label>
                    <input type="text" name="full_name" value="{{ $user->full_name ?? $user->name }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Phone</label>
                    <input type="tel" name="phone" value="{{ $user->phone }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                </div>

                <!-- Email (Read-only) -->
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Email</label>
                    <input type="email" value="{{ $user->email }}" readonly class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 text-slate-600">
                </div>

                <!-- Role (Read-only) -->
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Role</label>
                    <input type="text" value="{{ ucfirst($user->role) }}" readonly class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 text-slate-600">
                </div>

                <!-- Member Since (Read-only) -->
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Member Since</label>
                    <input type="text" value="{{ date('F d, Y', strtotime($user->created_at)) }}" readonly class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 text-slate-600">
                </div>
            </div>
        </form>

        <!-- Action Buttons & Password -->
        <div class="space-y-6">
            <!-- Action Buttons -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-semibold text-slate-800 mb-4">Actions</h3>
                <div class="space-y-3">
                    <button type="submit" form="profile-form" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                    <button type="button" onclick="document.getElementById('profile-form').reset()" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </button>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-semibold text-slate-800 mb-4">Change Password</h3>
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">New Password</label>
                        <input type="password" name="password" required
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition-all text-sm"
                            placeholder="Enter new password">
                    </div>
                    
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition-all text-sm"
                            placeholder="Confirm new password">
                    </div>
                    
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
