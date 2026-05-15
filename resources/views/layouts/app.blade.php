<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php $lodgeName = $appSettings->lodge_name ?? 'LodgeOS'; @endphp
    <title>@yield('title', $lodgeName) - {{ $lodgeName }}</title>
    @if(!empty($appSettings->favicon))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $appSettings->favicon) }}">
    @endif
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {}
                }
            }
        </script>
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans antialiased bg-slate-100">
    <div id="app" class="min-h-screen flex flex-col">
        @auth
            @include('partials.sidebar')
            <div class="lg:ml-64 flex-1 flex flex-col min-h-screen transition-all duration-300" id="main-content">
                @include('partials.header')
                <main class="flex-1 p-4 lg:p-8 overflow-auto">
                    <div class="mx-auto max-w-7xl">
                        @yield('content')
                    </div>
                </main>
                @include('partials.footer')
            </div>
        @else
            <main class="flex-1">
                @yield('content')
            </main>
        @endauth
    </div>
    {{-- Toast Notifications (success/error/warning/info from session flash + validation errors) --}}
    <x-notifications.container />

    {{-- Notifications Modal --}}
    <x-modals.notifications />

    @include('partials.scripts')
</body>
</html>
