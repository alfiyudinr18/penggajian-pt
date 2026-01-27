<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Penggajian') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">

    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

        <x-sidebar />

        <div class="flex-1 flex flex-col overflow-hidden relative">

            <x-navbar />

            <main class="flex-1 overflow-y-auto bg-gray-50 flex flex-col scroll-smooth">

                <div class="flex-1 p-4 sm:p-6 lg:p-8">

                    @if(session('success'))
                        <div class="mb-6 flex items-center p-4 text-green-800 rounded-lg bg-green-50 border-l-4 border-green-500 shadow-sm" role="alert">
                            <i class="fas fa-check-circle mr-3 text-lg"></i>
                            <div class="text-sm font-medium">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 flex items-center p-4 text-red-800 rounded-lg bg-red-50 border-l-4 border-red-500 shadow-sm" role="alert">
                            <i class="fas fa-exclamation-circle mr-3 text-lg"></i>
                            <div class="text-sm font-medium">{{ session('error') }}</div>
                        </div>
                    @endif

                    {{ $slot }}

                </div>

                <x-footer />

            </main>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html>
