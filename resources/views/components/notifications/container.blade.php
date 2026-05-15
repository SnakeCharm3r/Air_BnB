{{-- 
    Toast Notification Container
    Drop this once in your layout. It auto-renders flash messages from:
        session('success'), session('error'), session('warning'), session('info')
    And shows validation errors as a single error toast.
    
    Usage in layout:
        <x-notifications.container />
--}}
<div aria-live="assertive"
     class="pointer-events-none fixed inset-0 z-50 flex items-end px-4 py-6 sm:items-start sm:p-6">
    <div id="toast-stack" class="flex w-full flex-col items-center space-y-4 sm:items-end">

        @if(session('success'))
            <x-notifications.toast type="success" title="Success" :message="session('success')" />
        @endif

        @if(session('error'))
            <x-notifications.toast type="error" title="Error" :message="session('error')" />
        @endif

        @if(session('warning'))
            <x-notifications.toast type="warning" title="Warning" :message="session('warning')" />
        @endif

        @if(session('info'))
            <x-notifications.toast type="info" title="Notice" :message="session('info')" />
        @endif

        @if($errors->any())
            <x-notifications.toast 
                type="error" 
                title="Validation Error" 
                :message="$errors->first()" />
        @endif

    </div>
</div>

<script>
(function() {
    function showToast(el) {
        // Animate in
        requestAnimationFrame(() => {
            el.classList.remove('opacity-0', 'translate-y-2', 'sm:translate-x-2');
            el.classList.add('opacity-100', 'translate-y-0', 'sm:translate-x-0');
        });

        const dismiss = () => {
            el.classList.remove('opacity-100');
            el.classList.add('opacity-0');
            setTimeout(() => el.remove(), 300);
        };

        el.addEventListener('dismiss-toast', dismiss);

        const duration = parseInt(el.dataset.duration || 5000, 10);
        if (duration > 0) {
            setTimeout(dismiss, duration);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.toast-notification').forEach(showToast);
    });

    // Expose helper for JS-triggered toasts
    window.showToast = function({ type = 'success', title = '', message = '', duration = 5000 } = {}) {
        const stack = document.getElementById('toast-stack');
        if (!stack) return;

        const colors = {
            success: 'text-emerald-500',
            error:   'text-rose-500',
            warning: 'text-amber-500',
            info:    'text-sky-500',
        };
        const icons = {
            success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
            error:   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />',
            warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />',
            info:    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />',
        };

        const el = document.createElement('div');
        el.className = 'toast-notification pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black/5 transition-all duration-300 ease-out opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2';
        el.dataset.duration = duration;
        el.setAttribute('role', 'alert');
        el.innerHTML = `
            <div class="p-4">
                <div class="flex items-start">
                    <div class="shrink-0">
                        <svg class="size-6 ${colors[type] || colors.success}" fill="none" viewBox="0 0 24 24" stroke="currentColor">${icons[type] || icons.success}</svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        ${title ? `<p class="text-sm font-medium text-slate-900">${title}</p>` : ''}
                        ${message ? `<p class="mt-1 text-sm text-slate-500">${message}</p>` : ''}
                    </div>
                    <div class="ml-4 flex shrink-0">
                        <button type="button" class="toast-close inline-flex rounded-md bg-white text-slate-400 hover:text-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                            <span class="sr-only">Close</span>
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
        el.querySelector('.toast-close').addEventListener('click', () => el.dispatchEvent(new CustomEvent('dismiss-toast')));
        stack.appendChild(el);
        showToast(el);
    };
})();
</script>
