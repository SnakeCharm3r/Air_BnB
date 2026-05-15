@extends('layouts.app')

@section('title', 'Edit Staff')
@section('page-title', 'Edit Staff')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('staff.show', $staff->id) }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Staff Details
    </a>

    <!-- Edit Staff Form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-6">Edit Staff Member</h2>
            
            <form action="{{ route('staff.update', $staff->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Full Name -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                        <input type="text" id="full_name" name="full_name" required value="{{ $staff->full_name }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ $staff->email }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                        <input type="tel" id="phone" name="phone" value="{{ $staff->phone }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    
                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                        <select id="role" name="role" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">Select role</option>
                            <option value="receptionist" {{ $staff->role === 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                            <option value="cleaner" {{ $staff->role === 'cleaner' ? 'selected' : '' }}>Cleaner</option>
                            <option value="chef" {{ $staff->role === 'chef' ? 'selected' : '' }}>Chef</option>
                            <option value="security" {{ $staff->role === 'security' ? 'selected' : '' }}>Security</option>
                            <option value="gardener" {{ $staff->role === 'gardener' ? 'selected' : '' }}>Gardener</option>
                            <option value="manager" {{ $staff->role === 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="admin" {{ $staff->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="other" {{ $staff->role === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Department -->
                    <div>
                        <label for="department" class="block text-sm font-medium text-slate-700 mb-1">Department</label>
                        <input type="text" id="department" name="department" value="{{ $staff->department }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    
                    <!-- Hire Date -->
                    <div>
                        <label for="hire_date" class="block text-sm font-medium text-slate-700 mb-1">Hire Date</label>
                        <input type="date" id="hire_date" name="hire_date" value="{{ $staff->hire_date }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>
                
                <!-- Salary -->
                <div>
                    <label for="salary" class="block text-sm font-medium text-slate-700 mb-1">Salary</label>
                    <input type="number" id="salary" name="salary" min="0" step="0.01" value="{{ $staff->salary }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>
                
                <!-- Status -->
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="is_active" name="is_active" {{ $staff->is_active ? 'checked' : '' }}
                            class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                        <span class="text-sm text-slate-700">Active Status</span>
                    </label>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('staff.show', $staff->id) }}" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
