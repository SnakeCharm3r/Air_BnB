@extends('layouts.app')

@section('title', 'Add Menu Item')
@section('page-title', 'Add Menu Item')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center gap-2">
        <a href="{{ route('menu.index') }}" class="text-slate-500 hover:text-slate-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">Add Menu Item</h2>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('menu.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Name *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Categories *</label>
                    <div class="flex flex-wrap gap-4">
                        @foreach($categories as $slug => $label)
                            <label class="flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                                <input type="checkbox" name="categories[]" value="{{ $slug }}" class="rounded text-emerald-500 focus:ring-emerald-500">
                                <span class="text-sm text-slate-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Inventory Item</label>
                    <select name="inventory_item_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Not linked</option>
                        @foreach($inventoryItems as $inventoryItem)
                            <option value="{{ $inventoryItem->id }}">{{ $inventoryItem->name }} ({{ $inventoryItem->quantity }} {{ $inventoryItem->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Price *</label>
                    <input type="number" name="price" step="0.01" min="0" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Unit</label>
                    <input type="text" name="unit" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="e.g. plate, glass">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Image URL</label>
                    <input type="text" name="image" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="available" value="1" checked class="rounded text-emerald-500 focus:ring-emerald-500">
                    <label class="text-sm text-slate-700">Available</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="requires_chef" value="1" class="rounded text-emerald-500 focus:ring-emerald-500">
                    <label class="text-sm text-slate-700">Requires chef on duty</label>
                </div>
                <div class="md:col-span-2 text-sm text-slate-500">
                    Serving window is automatically derived from selected categories and kitchen hours.
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('menu.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition">Save Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
