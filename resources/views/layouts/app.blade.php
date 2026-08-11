<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? 'FIT CLUB | ' . $title : (isset($header) ? 'FIT CLUB | ' . trim(strip_tags($header)) : 'FIT CLUB') }}</title>

        <!-- Custom FIT CLUB Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ff5b00'><path d='M13 10V3L4 14h7v7l9-11h-7z'/></svg>">

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Outfit', sans-serif;
                background-color: #181a20;
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
                width: 350px;
                height: 350px;
                border-radius: 50%;
                background: rgba(255, 91, 0, 0.15);
                filter: blur(100px);
                z-index: -1;
            }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body x-data="{ showGymTerms: false }" class="antialiased text-white min-h-screen bg-[#181a20] relative overflow-x-hidden">
        <!-- Background Glow Orbs -->
        <div class="neon-blur-circle top-10 -left-20"></div>
        <div class="neon-blur-circle bottom-40 -right-20"></div>

        <!-- Reverted Uniform Linear Speed SVG Mask Overlay (Master Heartbeat Beam Animation) -->
        <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden opacity-85 w-full h-full">
            <svg class="w-full h-full" viewBox="0 0 1400 400" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">

                <defs>
                    <!-- 1. SVG Mask: ECG Line path as the Clipping Mask -->
                    <mask id="ecgLineMaskApp">
                        <path d="M -300 200 L 500 200 L 530 140 L 560 260 L 590 80 L 620 310 L 650 160 L 680 230 L 710 200 L 1700 200" 
                              fill="none" 
                              stroke="#ffffff" 
                              stroke-width="4.8" 
                              stroke-linecap="round"
                              stroke-linejoin="round" />
                    </mask>

                    <!-- 2. Compact 280px Beam Gradient -->
                    <linearGradient id="cometMaskGradientApp" x1="0%" y1="0%" x2="100%" y2="0%">
                        <!-- Faint Tail (0% to 75%) -->
                        <stop offset="0%" stop-color="#ff5b00" stop-opacity="0.0" />
                        <stop offset="25%" stop-color="#ff5b00" stop-opacity="0.15" />
                        <stop offset="60%" stop-color="#ff6b00" stop-opacity="0.45" />
                        <stop offset="78%" stop-color="#ff8b00" stop-opacity="0.75" />
                        
                        <!-- Glowing White Core Head Tip (85% to 95%) -->
                        <stop offset="88%" stop-color="#ffa000" stop-opacity="1.0" />
                        <stop offset="94%" stop-color="#ffffff" stop-opacity="1.0" />
                        
                        <!-- Smooth Fade Out (96% to 100%) -->
                        <stop offset="97%" stop-color="#ff7b00" stop-opacity="0.25" />
                        <stop offset="100%" stop-color="#ff5b00" stop-opacity="0.0" />
                    </linearGradient>
                </defs>

                <!-- Base Dim Balanced Center Line -->
                <path d="M -300 200 L 500 200 L 530 140 L 560 260 L 590 80 L 620 310 L 650 160 L 680 230 L 710 200 L 1700 200" 
                      fill="none" 
                      stroke="#ff5b00" 
                      stroke-width="2.5" 
                      stroke-opacity="0.25" />

                <!-- 3. Masked Beam Back to Reverted Uniform Linear Speed -->
                <g mask="url(#ecgLineMaskApp)" style="filter: drop-shadow(0 0 10px #ff5b00) drop-shadow(0 0 20px rgba(255, 91, 0, 0.7));">
                    <rect x="0" y="0" width="280" height="400" fill="url(#cometMaskGradientApp)">
                        <animateTransform attributeName="transform" 
                                          type="translate" 
                                          calcMode="linear"
                                          values="-280,0; 1420,0; 1420,0" 
                                          keyTimes="0; 0.8888; 1" 
                                          dur="4.5s" 
                                          repeatCount="indefinite" />
                    </rect>
                </g>
            </svg>
        </div>

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

        <!-- Global Gym Terms Consent Modal Component -->
        <x-gym-terms-modal />
    </body>
</html>
