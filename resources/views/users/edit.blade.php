@extends('layouts.app')

@section('title', 'Edit User - ' . $user->name)
@section('page-title', 'Edit User')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Users
    </a>

    <!-- Edit User Form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
                    <span class="text-lg font-bold text-amber-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">Edit User: {{ $user->full_name ?? $user->name }}</h2>
                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                </div>
            </div>
            
            <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-2">Personal Information</h3>
                        
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                            <input type="text" id="full_name" name="full_name" value="{{ $user->full_name }}"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="e.g. John Smith">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ $user->phone }}"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="e.g. +255 712 345 678">
                        </div>
                    </div>
                    
                    <!-- Account Information -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-2">Account Information</h3>
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                            <input type="text" id="name" name="name" value="{{ $user->name }}" required 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="e.g. johnsmith">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ $user->email }}" required 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="e.g. john@example.com">
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">New Password (leave blank to keep current)</label>
                            <input type="password" id="password" name="password" minlength="6"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="Enter new password (optional)">
                        </div>
                    </div>
                </div>
                
                <!-- Role & Status -->
                <div class="space-y-4 pt-4 border-t border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Role & Status</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="role" class="block text-sm font-medium text-slate-700 mb-1">User Role</label>
                            <select id="role" name="role" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="receptionist" {{ $user->role === 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                                <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Account Status</label>
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="is_active" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}
                                    class="w-4 h-4 text-amber-600 border-slate-300 rounded focus:ring-amber-500">
                                <label for="is_active" class="text-sm text-slate-600">Active (user can login)</label>
                            </div>
                            @if($user->id === auth()->id())
                                <p class="text-xs text-rose-500 mt-1">You cannot deactivate your own account.</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                    @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-lg text-sm font-medium transition">
                                Delete User
                            </button>
                        </form>
                    @else
                        <div></div>
                    @endif
                    <div class="flex items-center gap-3">
                        <a href="{{ route('users.index') }}" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                            Update User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
