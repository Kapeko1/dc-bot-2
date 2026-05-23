<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Albion Killboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-950 text-gray-100">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-gray-900 border-b border-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <a href="{{ route('killboard.index') }}" class="flex items-center">
                            <span class="text-xl font-bold bg-gradient-to-r from-red-500 to-orange-500 bg-clip-text text-transparent">
                                DC Killboard
                            </span>
                        </a>
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="{{ route('killboard.index') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('killboard.index') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                                All Kills
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
