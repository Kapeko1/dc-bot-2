<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Blood Chronicle - DC Killboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cinzel:400,600,700|space-grotesk:300,400,500,600,700|jetbrains-mono:400,500,600" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Prevent flash of unstyled content */
        .fade-in { animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body class="h-full text-[#E8DCC8] antialiased">
    <div class="min-h-full relative">
        <!-- Diagonal accent lines background -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
            <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-[#DC143C]/10 to-transparent blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-gradient-to-tr from-[#D4AF37]/10 to-transparent blur-3xl"></div>
            <div class="absolute top-1/2 left-1/3 w-1 h-[200%] bg-gradient-to-b from-transparent via-[#DC143C]/20 to-transparent transform -rotate-12"></div>
            <div class="absolute top-1/2 right-1/4 w-1 h-[200%] bg-gradient-to-b from-transparent via-[#D4AF37]/10 to-transparent transform rotate-12"></div>
        </div>

        <!-- Navigation -->
        <nav class="relative z-10 border-b-2 border-[#DC143C]/30 metal-gradient">
            <div class="absolute inset-0 bg-gradient-to-r from-[#2D2D2D]/95 via-[#1A0A0A]/95 to-[#2D2D2D]/95 backdrop-blur-sm"></div>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative">
                <div class="flex h-20 items-center justify-between">
                    <div class="flex items-center gap-12">
                        <a href="{{ route('killboard.index') }}" class="flex items-center gap-4 group">
                            <!-- Heraldic emblem -->
                            <div class="w-12 h-12 relative">
                                <div class="absolute inset-0 bg-gradient-to-br from-[#DC143C] to-[#8B0000] transform -rotate-45 border-2 border-[#D4AF37] shadow-lg shadow-[#DC143C]/50"></div>
                                <div class="absolute inset-2 bg-[#0A0A0A] transform -rotate-45 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-[#DC143C] transform rotate-45" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2L3 7v6c0 5.5 3.8 10.7 9 12 5.2-1.3 9-6.5 9-12V7l-9-5z"/>
                                    </svg>
                                </div>
                            </div>
                            <!-- Title with dramatic styling -->
                            <div class="flex flex-col">
                                <span class="font-[Cinzel] text-2xl font-bold tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-[#DC143C] via-[#EF4444] to-[#D4AF37] drop-shadow-[0_0_10px_rgba(220,20,60,0.5)] transition-all duration-300 group-hover:drop-shadow-[0_0_20px_rgba(220,20,60,0.8)]">
                                    DC KILLBOARD
                                </span>
                            </div>
                        </a>

                        <div class="flex items-baseline gap-6">
                            <a href="{{ route('killboard.index') }}"
                               class="relative px-4 py-2 font-[Space_Grotesk] text-sm font-medium tracking-wide uppercase transition-all duration-300 {{ request()->routeIs('killboard.index') ? 'text-[#DC143C]' : 'text-[#E8DCC8]/70 hover:text-[#DC143C]' }}">
                                <span class="relative z-10">Kill Log</span>
                                @if(request()->routeIs('killboard.index'))
                                    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-[#DC143C] to-transparent"></div>
                                    <div class="absolute inset-0 bg-[#DC143C]/10 transform -skew-x-12"></div>
                                @endif
                            </a>
                        </div>
                    </div>

                    <!-- Corner accent -->
                    <div class="hidden lg:flex items-center gap-2 font-[JetBrains_Mono] text-xs text-[#D4AF37]/60 tracking-wider">
                        <div class="w-2 h-2 bg-[#DC143C] animate-pulse"></div>
                        LIVE
                    </div>
                </div>
            </div>
            <!-- Bottom glow effect -->
            <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-[#DC143C] to-transparent opacity-50"></div>
        </nav>

        <!-- Page Content -->
        <main class="relative z-10">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 fade-in">
                @yield('content')
            </div>
        </main>

        <!-- Footer accent -->
        <footer class="relative z-10 mt-20 border-t border-[#DC143C]/20 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center gap-2">
                    <div class="w-8 h-0.5 bg-gradient-to-r from-transparent to-[#DC143C]"></div>
                    <div class="w-8 h-0.5 bg-gradient-to-r from-[#DC143C] to-transparent"></div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Simple animation trigger on load
        document.addEventListener('DOMContentLoaded', function() {
            // Add staggered animation classes to elements
            const elements = document.querySelectorAll('.animate-on-scroll');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>
