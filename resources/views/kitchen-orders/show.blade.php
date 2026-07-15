@extends('layouts.app')

@section('title', 'Kitchen Order Details')
@section('page-title', 'Kitchen Order Details')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Order #{{ $order->id }}</h2>
                <p class="text-sm text-slate-500">Placed {{ $order->created_at->diffForHumans() }}</p>
            </div>
            <span class="px-3 py-1 text-sm font-medium rounded-full capitalize
                {{ match($order->status) {
                    'pending' => 'bg-amber-100 text-amber-700',
                    'preparing' => 'bg-blue-100 text-blue-700',
                    'ready' => 'bg-violet-100 text-violet-700',
                    'delivered' => 'bg-emerald-100 text-emerald-700',
                    'cancelled' => 'bg-rose-100 text-rose-700',
                    default => 'bg-slate-100 text-slate-700'
                } }}">
                {{ $order->status }}
            </span>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500 uppercase">Menu Item</p>
                    <p class="text-sm font-medium text-slate-800">{{ $order->menuItem->name }}</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500 uppercase">Room</p>
                    <p class="text-sm font-medium text-slate-800">Room {{ $order->room->room_number ?? 'N/A' }}</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500 uppercase">Quantity</p>
                    <p class="text-sm font-medium text-slate-800">{{ $order->quantity }}</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500 uppercase">Total</p>
                    <p class="text-sm font-medium text-slate-800">{{ format_money($order->total_price) }}</p>
                </div>
            </div>

            @if($order->guest_name)
                <div class="p-3 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500 uppercase">Guest</p>
                    <p class="text-sm font-medium text-slate-800">{{ $order->guest_name }}</p>
                </div>
            @endif

            @if($order->notes)
                <div class="p-3 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500 uppercase">Notes</p>
                    <p class="text-sm text-slate-800">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <div class="mt-6 flex items-center justify-between">
            <a href="{{ route('kitchen-orders.index') }}" class="text-sm text-slate-600 hover:text-slate-800">← Back to orders</a>
            @if(auth()->user()->isChef() && in_array($order->status, ['pending', 'preparing', 'ready']))
                <form action="{{ route('kitchen-orders.status', $order->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="delivered">
                    <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition">Mark Delivered</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
