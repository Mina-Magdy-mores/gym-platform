<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FIT CLUB | Home</title>

        <!-- Custom FIT CLUB Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ff5b00'><path d='M13 10V3L4 14h7v7l9-11h-7z'/></svg>">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />

        <!-- Scripts & Tailwind -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- ScrollReveal JS -->
        <script src="https://unpkg.com/scrollreveal"></script>

        <style>
            body {
                font-family: 'Outfit', sans-serif;
                background-color: #181a20;
                color: #ffffff;
                overflow-x: hidden;
            }

            .text-outline {
                color: transparent;
                -webkit-text-stroke: 1.5px rgba(255, 255, 255, 0.8);
            }

            .neon-accent {
                color: #ff5b00;
                text-shadow: 0 0 15px rgba(255, 91, 0, 0.4);
            }

            .bg-neon-gradient {
                background: linear-gradient(135deg, #ff5b00 0%, #ff2a00 100%);
            }

            .bg-neon-glow {
                box-shadow: 0 0 30px rgba(255, 91, 0, 0.3);
            }

            .glass-card {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }

            .glass-card:hover {
                background: rgba(255, 255, 255, 0.05);
                border-color: rgba(255, 91, 0, 0.3);
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

            /* Smooth Scroll Offset */
            html {
                scroll-padding-top: 100px;
            }
        </style>
    </head>
    <body class="antialiased min-h-screen flex flex-col justify-between bg-[#181a20] relative">

        <!-- Circles for decoration background -->
        <div class="neon-blur-circle top-10 -left-20"></div>
        <div class="neon-blur-circle bottom-40 -right-20"></div>

        <!-- Reverted Uniform Linear Speed SVG Mask Overlay (Master Heartbeat Beam Animation) -->
        <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden opacity-85 w-full h-full">
            <svg class="w-full h-full" viewBox="0 0 1400 400" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">

                <defs>
                    <!-- 1. SVG Mask: ECG Line path as the Clipping Mask -->
                    <mask id="ecgLineMask">
                        <path d="M -300 200 L 500 200 L 530 140 L 560 260 L 590 80 L 620 310 L 650 160 L 680 230 L 710 200 L 1700 200" 
                              fill="none" 
                              stroke="#ffffff" 
                              stroke-width="4.8" 
                              stroke-linecap="round"
                              stroke-linejoin="round" />
                    </mask>

                    <!-- 2. Compact 280px Beam Gradient -->
                    <linearGradient id="cometMaskGradient" x1="0%" y1="0%" x2="100%" y2="0%">
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
                <g mask="url(#ecgLineMask)" style="filter: drop-shadow(0 0 10px #ff5b00) drop-shadow(0 0 20px rgba(255, 91, 0, 0.7));">
                    <rect x="0" y="0" width="280" height="400" fill="url(#cometMaskGradient)">
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

        <!-- Navbar Header -->
        <header class="fixed top-0 left-0 right-0 w-full z-50 glass-card px-6 py-4 transition-all duration-300">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-2 text-2xl font-black tracking-wider uppercase">
                    <span class="neon-accent">FIT</span><span>CLUB</span>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold tracking-wide">
                    <a href="#home" class="hover:text-[#ff5b00] transition duration-200">Home</a>
                    <a href="#program" class="hover:text-[#ff5b00] transition duration-200">Program</a>
                    <a href="#choose" class="hover:text-[#ff5b00] transition duration-200">Why Us</a>
                    <a href="#plans" class="hover:text-[#ff5b00] transition duration-200">Plans</a>
                    <a href="#schedules" class="hover:text-[#ff5b00] transition duration-200">Schedules</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2 rounded-full border border-white hover:border-[#ff5b00] hover:text-[#ff5b00] transition duration-300 text-sm font-bold">
                                Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-5 py-2 rounded-full bg-[#ff5b00] text-white hover:opacity-90 transition duration-300 text-sm font-bold bg-neon-glow cursor-pointer">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-bold hover:text-[#ff5b00] transition duration-200">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-5 py-2 rounded-full bg-neon-gradient text-white hover:opacity-90 transition duration-300 text-sm font-bold bg-neon-glow">
                                    Join Now
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <!-- Main Landing Content -->
        <main class="flex-grow pt-28 relative z-10">

            <!-- Hero Section -->
            <section id="home" class="max-w-6xl mx-auto px-6 py-12 md:py-24 grid grid-cols-1 md:grid-cols-2 gap-12 items-center relative">
                <!-- Left Content -->
                <div class="hero__data space-y-6">
                    <div class="inline-block px-4 py-1.5 rounded-full border border-[rgba(255,91,0,0.3)] bg-[rgba(255,91,0,0.05)] text-xs font-black uppercase tracking-wider neon-accent">
                        THE BEST FIT CLUB IN TOWN
                    </div>
                    <h1 class="text-5xl md:text-7xl font-black uppercase tracking-wide leading-none">
                        <span class="text-outline">MAKE</span> YOUR <br>
                        BODY SHAPE
                    </h1>
                    <p class="text-gray-400 text-base md:text-lg leading-relaxed max-w-md">
                        In here we will help you to shape and build your ideal body and live up your life to fullest. Let's make it happen.
                    </p>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('register') }}" class="px-8 py-3 rounded-full bg-neon-gradient text-white hover:opacity-90 transition duration-300 text-base font-bold bg-neon-glow">
                            Get Started
                        </a>
                        <a href="#program" class="px-8 py-3 rounded-full border border-white hover:border-[#ff5b00] hover:text-[#ff5b00] transition duration-300 text-base font-bold">
                            Learn More
                        </a>
                    </div>
                </div>

                <!-- Right Hero Image with Neon Glow Overlay -->
                <div class="hero__img justify-self-center md:justify-self-end">
                    <div class="relative group cursor-pointer">
                        <div class="absolute -inset-1 rounded-2xl bg-neon-gradient opacity-30 blur-xl group-hover:opacity-75 transition duration-500"></div>
                        <div class="relative w-[280px] md:w-[360px] h-[380px] md:h-[480px] rounded-2xl overflow-hidden shadow-2xl border border-[rgba(255,255,255,0.1)]">
                            <img src="{{ asset('images/gym_hero_athlete.jpg') }}" alt="Fit Club Athlete" class="w-full h-full object-cover grayscale brightness-90 transform group-hover:scale-105 group-hover:grayscale-0 transition duration-500">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Programs Section -->
            <section id="program" class="max-w-6xl mx-auto px-6 py-16 md:py-24 space-y-12">
                <div class="text-center space-y-2">
                    <h2 class="text-xs font-black uppercase tracking-widest neon-accent">OUR PROGRAM</h2>
                    <h3 class="text-3xl md:text-4xl font-black uppercase tracking-wide">BUILD YOUR BEST SHAPE</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Program 1 -->
                    <div class="program__card glass-card p-6 rounded-2xl space-y-4 hover:border-[#ff5b00] transition duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-2xl text-[#ff5b00] group-hover:bg-neon-gradient group-hover:text-white transition duration-300">
                            <i class="ri-heart-pulse-fill"></i>
                        </div>
                        <h4 class="text-xl font-bold uppercase">Flex Muscle</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            For those who want to shape their body through high resistance workouts and muscle building.
                        </p>
                        <a href="#plans" class="inline-flex items-center gap-2 text-xs font-bold text-[#ff5b00] group-hover:translate-x-1 transition duration-200">
                            <span>Join Now</span> <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>

                    <!-- Program 2 -->
                    <div class="program__card glass-card p-6 rounded-2xl space-y-4 hover:border-[#ff5b00] transition duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-2xl text-[#ff5b00] group-hover:bg-neon-gradient group-hover:text-white transition duration-300">
                            <i class="ri-fire-fill"></i>
                        </div>
                        <h4 class="text-xl font-bold uppercase">Cardio Exercise</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            Designed for fat loss, stamina endurance, and improving overall cardiovascular health.
                        </p>
                        <a href="#plans" class="inline-flex items-center gap-2 text-xs font-bold text-[#ff5b00] group-hover:translate-x-1 transition duration-200">
                            <span>Join Now</span> <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>

                    <!-- Program 3 -->
                    <div class="program__card glass-card p-6 rounded-2xl space-y-4 hover:border-[#ff5b00] transition duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-2xl text-[#ff5b00] group-hover:bg-neon-gradient group-hover:text-white transition duration-300">
                            <i class="ri-body-scan-fill"></i>
                        </div>
                        <h4 class="text-xl font-bold uppercase">Basic Yoga</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            Focus on posture alignment, mobility, breathing technique, and active joint recovery.
                        </p>
                        <a href="#plans" class="inline-flex items-center gap-2 text-xs font-bold text-[#ff5b00] group-hover:translate-x-1 transition duration-200">
                            <span>Join Now</span> <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>

                    <!-- Program 4 -->
                    <div class="program__card glass-card p-6 rounded-2xl space-y-4 hover:border-[#ff5b00] transition duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-2xl text-[#ff5b00] group-hover:bg-neon-gradient group-hover:text-white transition duration-300">
                            <i class="ri-shield-flash-fill"></i>
                        </div>
                        <h4 class="text-xl font-bold uppercase">Weight Lifting</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            Heavy compound powerlifting and athletic strength conditioning guided by certified trainers.
                        </p>
                        <a href="#plans" class="inline-flex items-center gap-2 text-xs font-bold text-[#ff5b00] group-hover:translate-x-1 transition duration-200">
                            <span>Join Now</span> <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Why Choose Us Section -->
            <section id="choose" class="max-w-6xl mx-auto px-6 py-16 md:py-28 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <!-- Left Interactive Dedicated Trainer Image -->
                <div class="choose__img justify-self-center md:justify-self-start">
                    <div class="relative group cursor-pointer">
                        <div class="absolute -inset-2 rounded-2xl bg-neon-gradient opacity-15 blur-2xl group-hover:opacity-60 transition duration-500"></div>
                        <div class="relative w-[300px] md:w-[400px] h-[350px] md:h-[450px] rounded-2xl overflow-hidden shadow-2xl border border-[rgba(255,255,255,0.05)] group-hover:border-[#ff5b00]/40 transition duration-500">
                            <img src="{{ asset('images/gym_trainer_coach.jpg') }}" alt="Dedicated Personal Trainer" class="w-full h-full object-cover grayscale brightness-90 transform group-hover:scale-110 group-hover:grayscale-0 transition duration-700 ease-out">
                        </div>
                    </div>
                </div>

                <!-- Right content -->
                <div class="choose__content space-y-6">
                    <div class="space-y-2">
                        <h2 class="text-xs font-black uppercase tracking-widest neon-accent">WHY CHOOSE US</h2>
                        <h3 class="text-3xl md:text-4xl font-black uppercase tracking-wide">Valuable Reasons to Join Us</h3>
                    </div>
                    <p class="text-gray-400 text-sm md:text-base leading-relaxed">
                        We provide state-of-the-art facilities, certified trainers, and structured digital modules for your training.
                    </p>

                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <span class="p-1 rounded-full bg-neon-gradient text-white text-xs mt-1">
                                <i class="ri-check-line"></i>
                            </span>
                            <div>
                                <h5 class="text-base font-bold">Personal Certified Trainers</h5>
                                <p class="text-xs text-gray-400">Customized one-on-one sessions with elite coaches.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="p-1 rounded-full bg-neon-gradient text-white text-xs mt-1">
                                <i class="ri-check-line"></i>
                            </span>
                            <div>
                                <h5 class="text-base font-bold">Flexible Modular Schedules</h5>
                                <p class="text-xs text-gray-400">Book your physical or digital sessions anytime.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="p-1 rounded-full bg-neon-gradient text-white text-xs mt-1">
                                <i class="ri-check-line"></i>
                            </span>
                            <div>
                                <h5 class="text-base font-bold">State of Art Equipment</h5>
                                <p class="text-xs text-gray-400">High performance machinery and clean environments.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Pricing Plans Section -->
            <section id="plans" class="max-w-6xl mx-auto px-6 py-16 md:py-28 space-y-12">
                <div class="text-center space-y-2">
                    <h2 class="text-xs font-black uppercase tracking-widest neon-accent">OUR PRICING PLAN</h2>
                    <h3 class="text-3xl md:text-4xl font-black uppercase tracking-wide">Choose Your Subscriptions</h3>
                </div>

                @if(session('error'))
                    <div class="p-4 rounded-xl bg-red-500/20 border border-red-500/30 text-red-400 font-bold text-xs flex items-center justify-center gap-2 max-w-2xl mx-auto">
                        <i class="ri-error-warning-fill text-lg"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($plans as $plan)
                        <x-subscription-plan-card :plan="$plan" :showSubscribeForm="false" />
                    @endforeach
                </div>
            </section>

            <!-- Gym Schedules Section -->
            <section id="schedules" class="max-w-6xl mx-auto px-6 py-16 space-y-8">
                <div class="text-center space-y-2">
                    <h2 class="text-xs font-black uppercase tracking-widest neon-accent">WORKING HOURS</h2>
                    <h3 class="text-3xl md:text-4xl font-black uppercase tracking-wide">Gym Operating Schedules</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Men Schedules Card -->
                    <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                        <div class="flex items-center justify-between border-b border-white/10 pb-4">
                            <h3 class="text-xl font-black text-white uppercase flex items-center gap-2">
                                <i class="ri-men-line text-blue-400 text-2xl"></i> Men's Operating Hours
                            </h3>
                            <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-400 text-xs font-black uppercase tracking-wider">
                                Men Shift Hours
                            </span>
                        </div>

                        <ul class="space-y-4 text-xs">
                            @forelse($menSchedules as $sch)
                                <li class="p-4 rounded-xl bg-white/5 flex items-center justify-between hover:bg-white/10 transition">
                                    <div>
                                        <div class="font-bold text-white text-sm">{{ $sch->days_label }}</div>
                                        <div class="text-gray-400 text-xs mt-0.5">{{ $sch->notes ?? 'Standard Men Shift' }}</div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-black text-[#ff5b00] text-sm block">{{ $sch->time_label }}</span>
                                    </div>
                                </li>
                            @empty
                                <li class="text-gray-400 text-xs italic">No schedule posted for Men.</li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Women Schedules Card -->
                    <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                        <div class="flex items-center justify-between border-b border-white/10 pb-4">
                            <h3 class="text-xl font-black text-[#ff5b00] uppercase flex items-center gap-2">
                                <i class="ri-women-line text-pink-400 text-2xl"></i> Women's Operating Hours
                            </h3>
                            <span class="px-3 py-1 rounded-full bg-pink-500/20 text-pink-400 text-xs font-black uppercase tracking-wider">
                                Women Shift Hours
                            </span>
                        </div>

                        <ul class="space-y-4 text-xs">
                            @forelse($womenSchedules as $sch)
                                <li class="p-4 rounded-xl bg-white/5 flex items-center justify-between hover:bg-white/10 transition">
                                    <div>
                                        <div class="font-bold text-white text-sm">{{ $sch->days_label }}</div>
                                        <div class="text-gray-400 text-xs mt-0.5">{{ $sch->notes ?? 'Standard Ladies Shift' }}</div>
                                    </div>
                                    <div class="text-right">
                                        @if($sch->is_off_day)
                                            <span class="px-3 py-1 rounded-full bg-red-500/20 text-red-400 font-black text-xs uppercase tracking-wider">
                                                Ladies Day Off
                                            </span>
                                        @else
                                            <span class="font-black text-[#ff5b00] text-sm block">{{ $sch->time_label }}</span>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="text-gray-400 text-xs italic">No schedule posted for Ladies.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="glass-card py-10 px-6 mt-12 border-t border-[rgba(255,255,255,0.05)] relative z-10">
            <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
                <!-- Logo -->
                <a href="#" class="text-xl font-black uppercase">
                    <span class="neon-accent">FIT</span><span>CLUB</span>
                </a>
                <p class="text-xs text-gray-400">
                    &copy; 2026 FIT CLUB. All rights reserved. Built with passion & modular design.
                </p>
                <div class="flex gap-4 text-gray-400 text-lg">
                    <a href="#" class="hover:text-white transition"><i class="ri-facebook-fill"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="ri-instagram-line"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="ri-twitter-fill"></i></a>
                </div>
            </div>
        </footer>

        <!-- ScrollReveal Animation Scripts -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof window.ScrollReveal !== 'undefined') {
                    const sr = window.ScrollReveal({
                        origin: 'top',
                        distance: '60px',
                        duration: 2000,
                        delay: 300,
                        reset: false
                    });

                    sr.reveal('.hero__data');
                    sr.reveal('.hero__img', { origin: 'bottom', delay: 500 });
                    sr.reveal('.program__card', { interval: 150 });
                    sr.reveal('.choose__img', { origin: 'left' });
                    sr.reveal('.choose__content', { origin: 'right', delay: 400 });
                    sr.reveal('.pricing__card', { interval: 150 });
                }
            });
        </script>
    </body>
</html>
