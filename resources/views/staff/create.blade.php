@extends('layouts.app')

@section('title', 'Add Staff')
@section('page-title', 'Add Staff')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('staff.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Staff
    </a>

    <!-- Add Staff Form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-6">Add New Staff Member</h2>
            
            <form action="{{ route('staff.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Full Name -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                        <input type="text" id="full_name" name="full_name" required 
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                            placeholder="e.g. John Doe">
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" 
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                            placeholder="e.g. john@example.com">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                        <input type="tel" id="phone" name="phone" 
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                            placeholder="e.g. +254 712 345 678">
                    </div>
                    
                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                        <select id="role" name="role" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">Select role</option>
                            <option value="receptionist">Receptionist</option>
                            <option value="cleaner">Cleaner</option>
                            <option value="chef">Chef</option>
                            <option value="security">Security</option>
                            <option value="gardener">Gardener</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Department -->
                    <div>
                        <label for="department" class="block text-sm font-medium text-slate-700 mb-1">Department</label>
                        <input type="text" id="department" name="department" 
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                            placeholder="e.g. Front Desk">
                    </div>
                    
                    <!-- Hire Date -->
                    <div>
                        <label for="hire_date" class="block text-sm font-medium text-slate-700 mb-1">Hire Date</label>
                        <input type="date" id="hire_date" name="hire_date" 
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>
                
                <!-- Salary -->
                <div>
                    <label for="salary" class="block text-sm font-medium text-slate-700 mb-1">Salary</label>
                    <input type="number" id="salary" name="salary" min="0" step="0.01"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        placeholder="e.g. 50000">
                </div>
                
                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('staff.index') }}" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        Add Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
