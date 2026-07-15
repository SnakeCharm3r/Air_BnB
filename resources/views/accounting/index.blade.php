@extends('layouts.app')

@section('title', 'Accounting')
@section('page-title', 'Accounting')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Accounting</h1>
        <p class="text-sm text-slate-500">Manage billing, payments, and receipts</p>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Billing Card -->
        <a href="{{ route('billing.index') }}" class="group block bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-amber-300 transition p-6">
            <div class="flex items-start justify-between">
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600">
                    @include('components.icons.billing', ['class' => 'w-6 h-6'])
                </div>
                <svg class="w-5 h-5 text-slate-400 group-hover:text-amber-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-slate-800 group-hover:text-amber-600 transition">Billing</h3>
            <p class="mt-1 text-sm text-slate-500">View outstanding bills, record payments, and add guest charges.</p>
        </a>

        <!-- Receipts Card -->
        <a href="{{ route('receipts.index') }}" class="group block bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-300 transition p-6">
            <div class="flex items-start justify-between">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600">
                    @include('components.icons.receipt', ['class' => 'w-6 h-6'])
                </div>
                <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-slate-800 group-hover:text-emerald-600 transition">Receipts</h3>
            <p class="mt-1 text-sm text-slate-500">View and print guest payment receipts with full stay details.</p>
        </a>

        <!-- Invoices Card -->
        <a href="{{ route('invoices.index') }}" class="group block bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-300 transition p-6">
            <div class="flex items-start justify-between">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-slate-800 group-hover:text-blue-600 transition">Invoices</h3>
            <p class="mt-1 text-sm text-slate-500">Issue, manage, and print guest invoices.</p>
        </a>
    </div>
</div>
@endsection
