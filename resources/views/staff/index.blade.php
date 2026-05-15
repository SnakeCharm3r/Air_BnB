@extends('layouts.app')

@section('title', 'Staff')
@section('page-title', 'Staff')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Staff Management</h2>
            <p class="text-sm text-slate-500">Manage employees and schedules</p>
        </div>
        <button class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
            + Add Staff
        </button>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center">
        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-slate-800">Staff Module</h3>
        <p class="text-slate-500 mt-2">Staff management features coming soon.</p>
    </div>
</div>
@endsection
