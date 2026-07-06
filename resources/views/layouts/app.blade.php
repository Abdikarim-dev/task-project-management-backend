<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'TaskFlow') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen">
    <div
        x-data="{ sidebarOpen: false }"
        class="flex min-h-screen"
        @keydown.escape.window="sidebarOpen = false"
    >
        <div
            x-show="sidebarOpen"
            x-cloak
            class="fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-sm lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <x-layout.sidebar />

        <div class="flex min-w-0 flex-1 flex-col">
            <x-layout.top-nav :title="$title ?? null" />

            <main class="flex-1 px-4 py-6 sm:px-6">
                @if (session('success'))
                    <div class="mb-6">
                        <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6">
                        <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
                    </div>
                @endif

                {{ $slot }}
            </main>

            <x-layout.footer />
        </div>
    </div>

    @stack('scripts')
</body>
</html>
