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
                background: rgba(255, 91, 0, 0.12);
                filter: blur(80px);
                z-index: 0;
            }
        </style>
    </head>
    <body class="antialiased text-white">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden bg-[#0b0b0c]">
            <!-- Decorative circles -->
            <div class="neon-blur-circle top-10 -left-20"></div>
            <div class="neon-blur-circle bottom-10 -right-20"></div>

            <div class="z-10">
                <a href="/" class="flex items-center gap-2 text-3xl font-black tracking-wider uppercase">
                    <span class="neon-accent">FIT</span><span>CLUB</span>
                </a>
            </div>

            <!-- Login / Register Card -->
            <div class="w-full sm:max-w-md mt-6 px-8 py-6 glass-card shadow-2xl overflow-hidden sm:rounded-2xl z-10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
