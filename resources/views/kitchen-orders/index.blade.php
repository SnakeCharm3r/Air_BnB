@extends('layouts.app')

@section('title', 'Kitchen Orders')
@section('page-title', 'Kitchen Orders')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Kitchen Orders</h2>
            <p class="text-sm text-slate-500">Track orders placed by reception and delivered to rooms.</p>
        </div>
        @if(auth()->user()->isReceptionist())
            <a href="{{ route('kitchen-orders.create') }}" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fas fa-plus mr-2"></i>New Order
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        @foreach($statuses as $status)
            @php
                $statusOrders = $orders->where('status', $status);
                $statusColors = [
                    'pending' => 'bg-amber-50 border-amber-200',
                    'preparing' => 'bg-blue-50 border-blue-200',
                    'ready' => 'bg-violet-50 border-violet-200',
                    'delivered' => 'bg-emerald-50 border-emerald-200',
                    'cancelled' => 'bg-rose-50 border-rose-200',
                ];
                $badgeColors = [
                    'pending' => 'bg-amber-100 text-amber-700',
                    'preparing' => 'bg-blue-100 text-blue-700',
                    'ready' => 'bg-violet-100 text-violet-700',
                    'delivered' => 'bg-emerald-100 text-emerald-700',
                    'cancelled' => 'bg-rose-100 text-rose-700',
                ];
            @endphp
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 {{ $statusColors[$status] ?? 'bg-slate-50' }}">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-slate-800 capitalize">{{ $status }}</h3>
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $badgeColors[$status] ?? 'bg-slate-100 text-slate-600' }}">{{ $statusOrders->count() }}</span>
                    </div>
                </div>
                <div class="divide-y divide-slate-100 max-h-[600px] overflow-y-auto">
                    @forelse($statusOrders as $order)
                        <div class="p-4 hover:bg-slate-50">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <p class="text-sm font-medium text-slate-800">{{ $order->menuItem->name }}</p>
                                    <p class="text-xs text-slate-500">Room {{ $order->room->room_number ?? 'N/A' }} @if($order->guest_name) - {{ $order->guest_name }} @endif</p>
                                </div>
                                <span class="text-sm font-semibold text-slate-800">{{ format_money($order->total_price) }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mb-3">
                                <span>Qty: {{ $order->quantity }}</span>
                                <span>•</span>
                                <span>{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                            @if($order->notes)
                                <p class="text-xs text-slate-500 mb-3">{{ $order->notes }}</p>
                            @endif
                            @if(auth()->user()->isChef() && in_array($order->status, ['pending', 'preparing', 'ready']))
                                <form action="{{ route('kitchen-orders.status', $order->id) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" onchange="this.form.submit()" class="w-full px-2 py-1.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                                        @if($order->status === 'pending')
                                            <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Mark Preparing</option>
                                            <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Mark Ready</option>
                                            <option value="delivered">Mark Delivered</option>
                                            <option value="cancelled">Cancel</option>
                                        @elseif($order->status === 'preparing')
                                            <option value="preparing" selected>Preparing</option>
                                            <option value="ready">Mark Ready</option>
                                            <option value="delivered">Mark Delivered</option>
                                            <option value="cancelled">Cancel</option>
                                        @elseif($order->status === 'ready')
                                            <option value="ready" selected>Ready</option>
                                            <option value="delivered">Mark Delivered</option>
                                            <option value="cancelled">Cancel</option>
                                        @endif
                                    </select>
                                </form>
                            @elseif($order->status === 'pending' && auth()->user()->isReceptionist())
                                <form action="{{ route('kitchen-orders.status', $order->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-700" onclick="return confirm('Cancel this order?')">Cancel order</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-sm">No {{ $status }} orders.</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
