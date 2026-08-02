<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FIT CLUB - Gym & Digital Platform</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Outfit', sans-serif;
                background-color: #0b0b0c;
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
        </style>
    </head>
    <body class="antialiased min-h-screen flex flex-col justify-between">

        <!-- Circles for decoration background -->
        <div class="neon-blur-circle top-10 -left-20"></div>
        <div class="neon-blur-circle bottom-40 -right-20"></div>

        <!-- Navbar Header -->
        <header class="fixed top-0 left-0 right-0 w-full z-50 glass-card px-6 py-4 transition-all duration-300">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <!-- Logo -->
                <a href="#" class="flex items-center gap-2 text-2xl font-black tracking-wider uppercase">
                    <span class="neon-accent">FIT</span><span>CLUB</span>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold tracking-wide">
                    <a href="#home" class="hover:text-[#ff5b00] transition duration-200">Home</a>
                    <a href="#program" class="hover:text-[#ff5b00] transition duration-200">Program</a>
                    <a href="#choose" class="hover:text-[#ff5b00] transition duration-200">Why Us</a>
                    <a href="#plans" class="hover:text-[#ff5b00] transition duration-200">Plans</a>
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
                                <button type="submit" class="px-5 py-2 rounded-full bg-neon-gradient text-white hover:opacity-90 transition duration-300 text-sm font-bold bg-neon-glow cursor-pointer">
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
        <main class="flex-grow pt-24">

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

                <!-- Right Image / Card -->
                <div class="hero__img relative justify-self-center md:justify-self-end">
                    <div class="relative w-[300px] md:w-[400px] h-[350px] md:h-[450px] rounded-2xl overflow-hidden shadow-2xl border border-[rgba(255,255,255,0.05)]">
                        <img src="/images/gym_hero_athlete.jpg" alt="Gym Athlete" class="w-full h-full object-cover">
                        <!-- Overlay Card -->
                        <div class="absolute bottom-6 left-6 right-6 p-4 rounded-xl glass-card flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="p-2.5 rounded-full bg-neon-gradient text-white text-xl">
                                    <i class="ri-heart-pulse-fill"></i>
                                </span>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Heart Rate</p>
                                    <p class="text-lg font-black tracking-wide">105 BPM</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400 font-bold uppercase">Calories</p>
                                <p class="text-lg font-black tracking-wide text-[#ff5b00]">220 kcal</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Programs Section -->
            <section id="program" class="max-w-6xl mx-auto px-6 py-16 md:py-28 space-y-12">
                <div class="text-center md:text-left space-y-2">
                    <h2 class="text-xs font-black uppercase tracking-widest neon-accent">EXPLORE OUR PROGRAM</h2>
                    <h3 class="text-3xl md:text-4xl font-black uppercase tracking-wide">To Shape Your Body</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Card 1 -->
                    <div class="program__card p-6 rounded-xl glass-card space-y-4 transition duration-300">
                        <span class="inline-block p-3 rounded-lg bg-[rgba(255,91,0,0.1)] text-[#ff5b00] text-3xl">
                            <i class="ri-fire-fill"></i>
                        </span>
                        <h4 class="text-xl font-bold tracking-wide">Strength Training</h4>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            In this program, you are trained to improve your strength through many exercises.
                        </p>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-sm font-bold neon-accent hover:underline">
                            <span>Join Now</span> <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                    <!-- Card 2 -->
                    <div class="program__card p-6 rounded-xl glass-card space-y-4 transition duration-300">
                        <span class="inline-block p-3 rounded-lg bg-[rgba(255,91,0,0.1)] text-[#ff5b00] text-3xl">
                            <i class="ri-heart-line"></i>
                        </span>
                        <h4 class="text-xl font-bold tracking-wide">Physical Fitness</h4>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Focusing on cardiorespiratory training to build high endurance and fat burning.
                        </p>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-sm font-bold neon-accent hover:underline">
                            <span>Join Now</span> <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                    <!-- Card 3 -->
                    <div class="program__card p-6 rounded-xl glass-card space-y-4 transition duration-300">
                        <span class="inline-block p-3 rounded-lg bg-[rgba(255,91,0,0.1)] text-[#ff5b00] text-3xl">
                            <i class="ri-run-line"></i>
                        </span>
                        <h4 class="text-xl font-bold tracking-wide">Fat Lose</h4>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Best routines for weight loss, high-intensity intervals, and functional health.
                        </p>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-sm font-bold neon-accent hover:underline">
                            <span>Join Now</span> <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                    <!-- Card 4 -->
                    <div class="program__card p-6 rounded-xl glass-card space-y-4 transition duration-300">
                        <span class="inline-block p-3 rounded-lg bg-[rgba(255,91,0,0.1)] text-[#ff5b00] text-3xl">
                            <i class="ri-capsule-line"></i>
                        </span>
                        <h4 class="text-xl font-bold tracking-wide">Weight Gain</h4>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            For those who want to bulk up properly with muscle mass and structured nutrition.
                        </p>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-sm font-bold neon-accent hover:underline">
                            <span>Join Now</span> <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Why Choose Us Section -->
            <section id="choose" class="max-w-6xl mx-auto px-6 py-16 md:py-28 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <!-- Left image -->
                <div class="choose__img justify-self-center md:justify-self-start">
                    <div class="relative w-[300px] md:w-[400px] h-[350px] md:h-[450px] rounded-2xl overflow-hidden shadow-2xl border border-[rgba(255,255,255,0.05)]">
                        <img src="/images/gym_hero_athlete.jpg" alt="Trainer" class="w-full h-full object-cover grayscale brightness-90">
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

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Plan 1 -->
                    <div class="pricing__card p-8 rounded-2xl glass-card flex flex-col justify-between space-y-6 transition duration-300">
                        <div class="space-y-4">
                            <h4 class="text-lg font-bold uppercase tracking-widest">Basic Plan</h4>
                            <p class="text-4xl font-black text-[#ff5b00]">$20 <span class="text-xs text-gray-400 font-normal uppercase">/ Month</span></p>
                            <hr class="border-gray-800">
                            <ul class="space-y-3 text-sm text-gray-300">
                                <li class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-[#ff5b00]"></i> <span>2 Hours fitness training</span></li>
                                <li class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-[#ff5b00]"></i> <span>Access to gym room</span></li>
                                <li class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-[#ff5b00]"></i> <span>Basic lockers</span></li>
                            </ul>
                        </div>
                        <a href="{{ route('register') }}" class="block text-center py-2.5 rounded-full border border-white hover:bg-white hover:text-black transition duration-300 font-bold">
                            Join Now
                        </a>
                    </div>

                    <!-- Plan 2 (Highlighted) -->
                    <div class="pricing__card p-8 rounded-2xl glass-card flex flex-col justify-between space-y-6 transition duration-300 border-[rgba(255,91,0,0.5)] relative bg-neon-glow">
                        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 px-4 py-1 rounded-full bg-neon-gradient text-white text-xs font-black uppercase tracking-widest">
                            BEST CHOICE
                        </div>
                        <div class="space-y-4">
                            <h4 class="text-lg font-bold uppercase tracking-widest">Weekly Plan</h4>
                            <p class="text-4xl font-black text-[#ff5b00]">$45 <span class="text-xs text-gray-400 font-normal uppercase">/ Week</span></p>
                            <hr class="border-gray-800">
                            <ul class="space-y-3 text-sm text-gray-300">
                                <li class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-[#ff5b00]"></i> <span>4 Hours fitness training</span></li>
                                <li class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-[#ff5b00]"></i> <span>Access to group classes</span></li>
                                <li class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-[#ff5b00]"></i> <span>Personal diet advisor</span></li>
                                <li class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-[#ff5b00]"></i> <span>Lockers and showers</span></li>
                            </ul>
                        </div>
                        <a href="{{ route('register') }}" class="block text-center py-2.5 rounded-full bg-neon-gradient text-white hover:opacity-90 transition duration-300 font-bold bg-neon-glow">
                            Join Now
                        </a>
                    </div>

                    <!-- Plan 3 -->
                    <div class="pricing__card p-8 rounded-2xl glass-card flex flex-col justify-between space-y-6 transition duration-300">
                        <div class="space-y-4">
                            <h4 class="text-lg font-bold uppercase tracking-widest">Monthly Plan</h4>
                            <p class="text-4xl font-black text-[#ff5b00]">$80 <span class="text-xs text-gray-400 font-normal uppercase">/ Month</span></p>
                            <hr class="border-gray-800">
                            <ul class="space-y-3 text-sm text-gray-300">
                                <li class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-[#ff5b00]"></i> <span>Unlimited fitness access</span></li>
                                <li class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-[#ff5b00]"></i> <span>All special gym programs</span></li>
                                <li class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-[#ff5b00]"></i> <span>1-on-1 Certified coach</span></li>
                                <li class="flex items-center gap-2"><i class="ri-checkbox-circle-fill text-[#ff5b00]"></i> <span>Free gym towel & bottle</span></li>
                            </ul>
                        </div>
                        <a href="{{ route('register') }}" class="block text-center py-2.5 rounded-full border border-white hover:bg-white hover:text-black transition duration-300 font-bold">
                            Join Now
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="glass-card py-10 px-6 mt-12 border-t border-[rgba(255,255,255,0.05)]">
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
                // التأكد من عمل المكتبة بشكل صحيح
                if (typeof window.ScrollReveal !== 'undefined') {
                    const sr = window.ScrollReveal({
                        origin: 'top',
                        distance: '60px',
                        duration: 2000,
                        delay: 300,
                        reset: false
                    });

                    // 1. تحريكات البطل (Hero)
                    sr.reveal('.hero__data');
                    sr.reveal('.hero__img', { origin: 'bottom', delay: 500 });

                    // 2. تحريكات البرامج (Programs)
                    sr.reveal('.program__card', { interval: 150 });

                    // 3. تحريكات لماذا نحن (Why Us)
                    sr.reveal('.choose__img', { origin: 'left' });
                    sr.reveal('.choose__content', { origin: 'right', delay: 400 });

                    // 4. تحريكات بطاقات الأسعار (Pricing)
                    sr.reveal('.pricing__card', { interval: 150 });
                }
            });
        </script>
    </body>
</html>
