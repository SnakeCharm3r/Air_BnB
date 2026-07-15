 @extends('layouts.app')

@section('title', 'Staff Details - ' . $staff->full_name)
@section('page-title', 'Staff Details')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('staff.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Staff
    </a>

    <!-- Staff Details Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center">
                        @include('components.icons.staff', ['class' => 'w-8 h-8 text-slate-500'])
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">{{ $staff->full_name }}</h1>
                        <p class="text-slate-500 mt-1">{{ $staff->role }} @if($staff->department) - {{ $staff->department }} @endif</p>
                    </div>
                </div>
                @if($staff->is_active)
                    <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-medium rounded-full">Active</span>
                @else
                    <span class="inline-block px-3 py-1 bg-rose-100 text-rose-700 text-sm font-medium rounded-full">Inactive</span>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-2">Personal Information</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Full Name</label>
                            <p class="text-sm text-slate-800">{{ $staff->full_name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Email</label>
                            <p class="text-sm text-slate-800">{{ $staff->email ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Phone</label>
                            <p class="text-sm text-slate-800">{{ $staff->phone ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Hire Date</label>
                            <p class="text-sm text-slate-800">{{ $staff->hire_date ? \Carbon\Carbon::parse($staff->hire_date)->format('F d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Work Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-2">Work Information</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Role</label>
                            <p class="text-sm text-slate-800 capitalize">{{ $staff->role }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Department</label>
                            <p class="text-sm text-slate-800">{{ $staff->department ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Salary</label>
                            <p class="text-sm text-slate-800">{{ $staff->salary ? format_money($staff->salary) : 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                            @if($staff->is_active)
                                <p class="text-sm text-emerald-600">Active</p>
                            @else
                                <p class="text-sm text-rose-600">Inactive</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex items-center gap-3 pt-6 border-t border-slate-200">
                <a href="{{ route('staff.edit', $staff->id) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                    Edit Staff
                </a>
                <a href="{{ route('staff.attendance', $staff->id) }}" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                    View Attendance
                </a>
            </div>
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Recent Attendance Records</h3>
            
            @if($attendance->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Check In</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Check Out</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Hours</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($attendance as $record)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm text-slate-800">{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $record->check_in ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $record->check_out ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $record->hours_worked ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-slate-400">
                    <p class="text-sm">No attendance records found</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
