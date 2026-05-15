@extends('layouts.app')

@section('title', 'Staff Attendance - ' . $staff->full_name)
@section('page-title', 'Staff Attendance')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('staff.show', $staff->id) }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Staff Details
    </a>

    <!-- Staff Info Header -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center">
                @include('components.icons.staff', ['class' => 'w-6 h-6 text-slate-500'])
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">{{ $staff->full_name }}</h1>
                <p class="text-slate-500">{{ $staff->role }} @if($staff->department) - {{ $staff->department }} @endif</p>
            </div>
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-800">Attendance Records (Last 30 Days)</h3>
                <button class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                    + Add Attendance
                </button>
            </div>
            
            @if($records->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Check In</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Check Out</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Hours Worked</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($records as $record)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm text-slate-800">{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $record->check_in ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $record->check_out ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $record->hours_worked ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if($record->check_in && $record->check_out)
                                            <span class="inline-block px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full">Complete</span>
                                        @elseif($record->check_in && !$record->check_out)
                                            <span class="inline-block px-2 py-1 bg-amber-100 text-amber-700 text-xs rounded-full">In Progress</span>
                                        @else
                                            <span class="inline-block px-2 py-1 bg-slate-100 text-slate-700 text-xs rounded-full">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-slate-400">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm">No attendance records found</p>
                    <p class="text-xs mt-1">Click "Add Attendance" to record the first attendance entry</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
