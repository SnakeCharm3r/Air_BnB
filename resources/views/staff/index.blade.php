@extends('layouts.app')

@section('title', 'Staff')
@section('page-title', 'Staff')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Staff Management</h2>
            <p class="text-sm text-slate-500">Manage lodge staff members</p>
        </div>
        <a href="{{ route('staff.create') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
            + Add Staff
        </a>
    </div>

    <!-- Staff Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($staff as $member)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center">
                                        @include('components.icons.staff', ['class' => 'w-5 h-5 text-slate-500'])
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-slate-800">{{ $member->name }}</div>
                                        <div class="text-xs text-slate-500">Hired {{ $member->hire_date ? \Carbon\Carbon::parse($member->hire_date)->format('M d, Y') : 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-800">{{ $member->email ?? '-' }}</div>
                                @if($member->phone)
                                    <div class="text-sm text-slate-500">{{ $member->phone }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-800 capitalize">{{ $member->role }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-600">{{ $member->department ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($member->status === 'active')
                                    <span class="inline-block px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full">Active</span>
                                @else
                                    <span class="inline-block px-2 py-1 bg-rose-100 text-rose-700 text-xs rounded-full">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('staff.show', $member->id) }}" class="text-slate-400 hover:text-blue-600 mr-3 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('staff.edit', $member->id) }}" class="text-slate-400 hover:text-amber-600 mr-3 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('staff.destroy', $member->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-rose-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    @include('components.icons.staff', ['class' => 'w-8 h-8 text-slate-300'])
                                </div>
                                <p class="text-sm font-medium text-slate-500">No staff found</p>
                                <p class="text-xs text-slate-400 mt-1">Click "Add Staff" to create your first staff member</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
