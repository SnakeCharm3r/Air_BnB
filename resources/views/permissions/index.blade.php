@extends('layouts.app')

@section('title', 'Roles & Permissions')
@section('page-title', 'Roles & Permissions')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Roles & Permissions</h2>
            <p class="text-sm text-slate-500">Control what each role can do across every module</p>
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Role Tabs --}}
    <div x-data="{ activeRole: '{{ $roles->first()?->id }}' }">

        <div class="flex gap-2 mb-6 flex-wrap">
            @foreach($roles as $role)
                <button
                    @click="activeRole = '{{ $role->id }}'"
                    :class="activeRole === '{{ $role->id }}'
                        ? 'bg-amber-500 text-white shadow-sm'
                        : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                    class="px-5 py-2 rounded-lg text-sm font-medium transition capitalize">
                    {{ ucfirst($role->name) }}
                    <span class="ml-1.5 text-xs opacity-75">({{ $role->permissions->count() }} perms)</span>
                </button>
            @endforeach
        </div>

        @foreach($roles as $role)
            <div x-show="activeRole === '{{ $role->id }}'" x-cloak>
                <form action="{{ route('permissions.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                            <h3 class="font-semibold text-slate-800 capitalize">
                                {{ ucfirst($role->name) }} — Permission Matrix
                            </h3>
                            @if($role->name !== 'admin')
                                <button type="submit"
                                    class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                                    Save Changes
                                </button>
                            @else
                                <span class="text-xs text-slate-400 italic">Admin has all permissions (read-only)</span>
                            @endif
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase w-48">Module</th>
                                        @php
                                            $allActions = collect($permissions->flatten())->map(fn($p) => explode('.', $p->name)[1])->unique()->sort()->values();
                                        @endphp
                                        @foreach($allActions as $action)
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase capitalize">{{ $action }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($modules as $moduleKey => $moduleLabel)
                                        @if($permissions->has($moduleKey))
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-6 py-3 font-medium text-slate-700">{{ $moduleLabel }}</td>
                                                @foreach($allActions as $action)
                                                    <td class="px-4 py-3 text-center">
                                                        @php
                                                            $permName = "{$moduleKey}.{$action}";
                                                            $permExists = $permissions->get($moduleKey)?->contains('name', $permName);
                                                            $hasIt = $role->permissions->contains('name', $permName);
                                                        @endphp
                                                        @if($permExists)
                                                            <label class="inline-flex items-center justify-center cursor-pointer">
                                                                <input
                                                                    type="checkbox"
                                                                    name="permissions[]"
                                                                    value="{{ $permName }}"
                                                                    {{ $hasIt ? 'checked' : '' }}
                                                                    {{ $role->name === 'admin' ? 'disabled' : '' }}
                                                                    class="w-4 h-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500 cursor-pointer">
                                                            </label>
                                                        @else
                                                            <span class="text-slate-200">—</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($role->name !== 'admin')
                            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end">
                                <button type="submit"
                                    class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                                    Save Changes
                                </button>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        @endforeach

    </div>

    {{-- Legend --}}
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-800">
        <strong>Role hierarchy:</strong> Admin → Manager → Receptionist → Chef.
        Changes here take effect immediately and apply to all users with that role.
        The Admin role always has full access and cannot be modified.
    </div>

</div>
@endsection
