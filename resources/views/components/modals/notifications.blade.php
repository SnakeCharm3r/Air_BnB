<!-- Notifications Modal -->
<div id="notifications-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div onclick="toggleNotificationsModal()" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
    
    <!-- Modal Content -->
    <div id="notifications-content" class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-xl transform transition-transform duration-300 ease-in-out">
        <div class="flex flex-col h-full">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-800">Notifications</h2>
                <button onclick="toggleNotificationsModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Notification List -->
            <div class="flex-1 overflow-y-auto">
                @if(session()->has('notifications'))
                    @foreach(session('notifications') as $notification)
                        <div class="px-6 py-4 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                            <div class="flex items-start gap-3">
                                <!-- Icon based on type -->
                                @if($notification['type'] === 'success')
                                    <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @elseif($notification['type'] === 'error')
                                    <div class="w-8 h-8 bg-rose-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </div>
                                @elseif($notification['type'] === 'warning')
                                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @endif
                                
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-800">{{ $notification['title'] ?? 'Notification' }}</p>
                                    <p class="text-sm text-slate-600 mt-0.5">{{ $notification['message'] }}</p>
                                    @if(isset($notification['time']))
                                        <p class="text-xs text-slate-400 mt-1">{{ $notification['time'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="px-6 py-12 text-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            @include('components.icons.notifications', ['class' => 'w-6 h-6 text-slate-400'])
                        </div>
                        <p class="text-sm text-slate-500">No notifications yet</p>
                    </div>
                @endif
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                <button onclick="clearNotifications()" class="w-full text-sm text-slate-600 hover:text-slate-800 font-medium transition-colors">
                    Clear all notifications
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleNotificationsModal() {
    const modal = document.getElementById('notifications-modal');
    const content = document.getElementById('notifications-content');
    
    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    } else {
        content.classList.add('translate-x-full');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
        document.body.style.overflow = '';
    }
}

function clearNotifications() {
    // Clear notifications from session (would need AJAX call to backend)
    // For now, just close the modal
    toggleNotificationsModal();
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('notifications-modal');
        if (!modal.classList.contains('hidden')) {
            toggleNotificationsModal();
        }
    }
});
</script>
