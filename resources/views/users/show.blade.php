@extends('layouts.app')

@section('title', $user->full_name ?? $user->name)
@section('page-title', 'User Details')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Users
    </a>

    <!-- User Profile Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold text-amber-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">{{ $user->full_name ?? $user->name }}</h1>
                        <p class="text-slate-500">{{ $user->email }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                            @if($user->is_active)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-600">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('users.edit', $user->id) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition">
                        Edit User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Account Information -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Account Information</h3>
            <div class="space-y-4">
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-sm text-slate-500">Username</span>
                    <span class="text-sm font-medium text-slate-800">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-sm text-slate-500">Email</span>
                    <span class="text-sm font-medium text-slate-800">{{ $user->email }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-sm text-slate-500">Role</span>
                    <span class="text-sm font-medium text-slate-800">{{ ucfirst($user->role) }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-sm text-slate-500">Status</span>
                    <span class="text-sm font-medium {{ $user->is_active ? 'text-emerald-600' : 'text-slate-500' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">Member Since</span>
                    <span class="text-sm font-medium text-slate-800">{{ date('F d, Y', strtotime($user->created_at)) }}</span>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Contact Information</h3>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase">Phone</p>
                        <p class="text-sm font-medium text-slate-800">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase">Email</p>
                        <p class="text-sm font-medium text-slate-800">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
