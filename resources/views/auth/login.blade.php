<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php $lodgeName = $appSettings->lodge_name ?? 'LodgeOS'; @endphp
    <title>Login - {{ $lodgeName }}</title>
    @if(!empty($appSettings->favicon))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $appSettings->favicon) }}">
    @endif
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans antialiased bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="flex items-center justify-center gap-3 mb-8">
            <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg overflow-hidden">
                @if(!empty($appSettings->login_logo))
                    <img src="{{ asset('storage/' . $appSettings->login_logo) }}" alt="{{ $lodgeName }}" class="w-full h-full object-cover">
                @elseif(!empty($appSettings->lodge_logo))
                    <img src="{{ asset('storage/' . $appSettings->lodge_logo) }}" alt="{{ $lodgeName }}" class="w-full h-full object-cover">
                @else
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                @endif
            </div>
            <div class="text-center">
                <h1 class="text-2xl font-bold text-slate-800">{{ $lodgeName }}</h1>
                <p class="text-sm text-slate-500">Management System</p>
            </div>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
            <h2 class="text-xl font-semibold text-slate-800 mb-6 text-center">Sign In</h2>

            @if($errors->any())
                <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-lg text-rose-600 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition-all"
                        placeholder="owner@lodge.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition-all"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 px-4 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition-colors shadow-md hover:shadow-lg">
                    Sign In
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-500">
                    Default: <span class="font-medium">owner@lodge.com</span> / <span class="font-medium">password123</span>
                </p>
            </div>
        </div>

        <p class="text-center text-sm text-slate-400 mt-6">
            © {{ date('Y') }} {{ $lodgeName }}. All rights reserved.
        </p>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
            }
        }

        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('error-message');
            const btn = document.getElementById('login-btn');
            const spinner = document.getElementById('loading-spinner');
            const btnText = btn.querySelector('span');
            
            // Show loading
            btn.disabled = true;
            btnText.textContent = 'Signing in...';
            spinner.classList.remove('hidden');
            errorDiv.classList.add('hidden');
            
            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Invalid credentials');
                }
                
                // Store token
                localStorage.setItem('auth_token', data.token);
                localStorage.setItem('user', JSON.stringify(data.user));
                
                // Redirect to dashboard
                window.location.href = '/dashboard';
                
            } catch (error) {
                errorDiv.textContent = error.message;
                errorDiv.classList.remove('hidden');
                btn.disabled = false;
                btnText.textContent = 'Sign In';
                spinner.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
