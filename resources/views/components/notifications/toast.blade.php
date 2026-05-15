{{-- 
    Reusable Toast Notification Component
    Based on Tailwind UI notification pattern: https://tailwindcss.com/plus/ui-blocks/application-ui/overlays/notifications
    
    Usage:
        <x-notifications.toast type="success" title="Saved" message="Changes saved successfully" />
        <x-notifications.toast type="error" title="Error" message="Failed to save" />
        <x-notifications.toast type="warning" title="Warning" message="Please review" />
        <x-notifications.toast type="info" title="Info" message="Heads up" />
    
    Props:
        - type: success | error | warning | info  (default: success)
        - title: string heading
        - message: string body
        - duration: ms before auto-dismiss (default: 5000, set to 0 to disable)
--}}
@props([
    'type' => 'success',
    'title' => '',
    'message' => '',
    'duration' => 5000,
])

@php
    $config = [
        'success' => [
            'iconBg' => 'text-emerald-500',
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
        ],
        'error' => [
            'iconBg' => 'text-rose-500',
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />',
        ],
        'warning' => [
            'iconBg' => 'text-amber-500',
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />',
        ],
        'info' => [
            'iconBg' => 'text-sky-500',
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />',
        ],
    ];
    $current = $config[$type] ?? $config['success'];
    $toastId = 'toast-' . uniqid();
@endphp

<div id="{{ $toastId }}"
     role="alert"
     data-duration="{{ $duration }}"
     class="toast-notification pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black/5 transition-all duration-300 ease-out opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2">
    <div class="p-4">
        <div class="flex items-start">
            <div class="shrink-0">
                <svg class="size-6 {{ $current['iconBg'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    {!! $current['icon'] !!}
                </svg>
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
                @if($title)
                    <p class="text-sm font-medium text-slate-900">{{ $title }}</p>
                @endif
                @if($message)
                    <p class="mt-1 text-sm text-slate-500">{{ $message }}</p>
                @endif
            </div>
            <div class="ml-4 flex shrink-0">
                <button type="button"
                        onclick="document.getElementById('{{ $toastId }}').dispatchEvent(new CustomEvent('dismiss-toast'))"
                        class="inline-flex rounded-md bg-white text-slate-400 hover:text-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                    <span class="sr-only">Close</span>
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
