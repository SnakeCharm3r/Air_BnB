@extends('layouts.app')

@section('title', 'Add New User')
@section('page-title', 'Add New User')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Users
    </a>

    <!-- Create User Form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-6">Create New User Account</h2>
            
            <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-2">Personal Information</h3>
                        
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                            <input type="text" id="full_name" name="full_name" 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="e.g. John Smith">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                            <input type="tel" id="phone" name="phone" 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="e.g. +255 712 345 678">
                        </div>
                    </div>
                    
                    <!-- Account Information -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-2">Account Information</h3>
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Username *</label>
                            <input type="text" id="name" name="name" required 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="e.g. johnsmith">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address *</label>
                            <input type="email" id="email" name="email" required 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="e.g. john@example.com">
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password *</label>
                            <input type="password" id="password" name="password" required minlength="6"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="Minimum 6 characters">
                        </div>
                    </div>
                </div>
                
                <!-- Role & Status -->
                <div class="space-y-4 pt-4 border-t border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Role & Status</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="role" class="block text-sm font-medium text-slate-700 mb-1">User Role *</label>
                            <select id="role" name="role" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="">Select a role</option>
                                <option value="admin">Administrator</option>
                                <option value="manager">Manager</option>
                                <option value="receptionist">Receptionist</option>
                                <option value="chef">Chef</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Account Status</label>
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="is_active" name="is_active" value="1" checked
                                    class="w-4 h-4 text-amber-600 border-slate-300 rounded focus:ring-amber-500">
                                <label for="is_active" class="text-sm text-slate-600">Active (user can login)</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('users.index') }}" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
