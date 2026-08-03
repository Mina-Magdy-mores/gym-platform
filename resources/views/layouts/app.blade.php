<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FIT CLUB') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Outfit', sans-serif;
                background-color: #0b0b0c;
                color: #ffffff;
            }
            .neon-accent {
                color: #ff5b00;
                text-shadow: 0 0 15px rgba(255, 91, 0, 0.4);
            }
            .bg-neon-gradient {
                background: linear-gradient(135deg, #ff5b00 0%, #ff2a00 100%);
            }
            .glass-card {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
            .neon-blur-circle {
                position: absolute;
                width: 300px;
                height: 300px;
                border-radius: 50%;
                background: rgba(255, 91, 0, 0.1);
                filter: blur(90px);
                z-index: 0;
            }
        </style>
    </head>
    <body class="antialiased text-white min-h-screen bg-[#0b0b0c] relative overflow-x-hidden">
        <!-- Background Glow Orbs -->
        <div class="neon-blur-circle top-10 -left-20"></div>
        <div class="neon-blur-circle bottom-20 -right-20"></div>

        <div class="min-h-screen flex flex-col justify-between relative z-10">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="glass-card border-b border-white/5 py-6 shadow-lg">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-grow py-8">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="glass-card py-6 px-6 border-t border-white/5 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} FIT CLUB. All rights reserved.
            </footer>
        </div>
    </body>
</html>
