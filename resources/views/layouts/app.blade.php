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
    <body x-data="{ sidebarOpen: localStorage.getItem('fitclub_sidebar_open') !== 'false', mobileSidebarOpen: false, showGymTerms: false }" class="antialiased text-white min-h-screen bg-[#181a20] relative overflow-x-hidden">
        <!-- Master cPanel Sidebar -->
        @include('layouts.sidebar')

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

        <!-- Dynamic Content Wrapper (Squeezes / Shifts with Sidebar State) -->
        <div
            :class="sidebarOpen ? 'md:pl-64' : 'md:pl-20'"
            class="min-h-screen flex flex-col justify-between relative z-10 transition-all duration-300 ease-in-out"
        >
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="glass-card border-b border-white/5 py-6 shadow-lg">
                    <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8">
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

        <!-- Global Real-Time WebSockets Toast Alert Component -->
        <div x-data="{
            toastMessage: null,
            showToast: false,
            init() {
                // Initialize Alpine Store Count on Page Load
                if (window.Alpine && Alpine.store('unreadChat')) {
                    Alpine.store('unreadChat').setCount({{ auth()->check() ? (new \Modules\Chat\Services\ChatService())->getTotalUnreadCount(auth()->id()) : 0 }});
                }

                if (window.Echo && {{ auth()->check() ? 'true' : 'false' }}) {
                    // 1. Join Community Presence Channel for Live Online/Offline Status
                    window.Echo.join('gym-community')
                        .here((users) => {
                            if (window.Alpine && Alpine.store('presence')) {
                                Alpine.store('presence').setOnline(users);
                            }
                        })
                        .joining((user) => {
                            if (window.Alpine && Alpine.store('presence')) {
                                Alpine.store('presence').add(user);
                            }
                        })
                        .leaving((user) => {
                            if (window.Alpine && Alpine.store('presence')) {
                                Alpine.store('presence').remove(user);
                            }
                        });

                    // 2. Private User Channel for Chat & Notifications
                    window.Echo.private('user.{{ auth()->id() }}')
                        .listen('.message.sent', (data) => {
                            // Update global Alpine store dynamically
                            if (window.Alpine && Alpine.store('unreadChat')) {
                                Alpine.store('unreadChat').increment();
                            }

                            // Broadcast custom event for live index updates
                            window.dispatchEvent(new CustomEvent('chat-message-received', { detail: data }));

                            if (!window.location.pathname.includes('/chats/' + data.conversation_id)) {
                                this.toastMessage = data;
                                this.showToast = true;
                                this.playNotificationPing();
                                setTimeout(() => { this.showToast = false; }, 6000);
                            }
                        });
                }
            },
            playNotificationPing() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, ctx.currentTime);
                    gain.gain.setValueAtTime(0.15, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.4);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.4);
                } catch(e) {}
            }
        }">
            <div x-show="showToast" x-transition.opacity.scale.90 class="fixed top-5 right-5 z-[9999] max-w-sm w-full glass-card p-4 rounded-2xl border border-[#ff5b00]/50 bg-[#12141c]/95 shadow-[0_20px_50px_rgba(255,91,0,0.3)] backdrop-blur-xl flex items-start gap-3.5" x-cloak>
                <div class="w-10 h-10 rounded-xl bg-neon-gradient flex items-center justify-center text-white font-black text-sm uppercase shrink-0 overflow-hidden">
                    <template x-if="toastMessage && toastMessage.sender_avatar">
                        <img :src="toastMessage.sender_avatar" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!toastMessage || !toastMessage.sender_avatar">
                        <span x-text="toastMessage ? toastMessage.sender_name.charAt(0) : 'U'"></span>
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-black text-white uppercase tracking-wider truncate" x-text="toastMessage ? toastMessage.sender_name : ''"></h4>
                        <span class="text-[9px] font-bold text-[#ff5b00] uppercase">Just Now</span>
                    </div>
                    <p class="text-xs text-gray-300 truncate mt-0.5" x-text="toastMessage ? (toastMessage.message || '[Image Attachment]') : ''"></p>
                    <a :href="toastMessage ? '/chats/' + toastMessage.conversation_id : '#'" class="inline-flex items-center gap-1 text-[10px] font-black text-[#ff5b00] uppercase tracking-wider mt-2 hover:underline">
                        <span>Reply Live</span>
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
                <button @click="showToast = false" type="button" class="text-gray-400 hover:text-white text-sm">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>
    </body>
</html>
