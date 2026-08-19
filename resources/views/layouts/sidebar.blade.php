<!-- Master cPanel Sidebar Component -->
<aside
    x-cloak
    @click.outside="if (sidebarOpen && window.innerWidth >= 768) { sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); }"
    :class="{
        'w-64': sidebarOpen,
        'w-20': !sidebarOpen,
        'translate-x-0': mobileSidebarOpen,
        '-translate-x-full md:translate-x-0': !mobileSidebarOpen
    }"
    class="fixed top-0 left-0 h-full bg-[#0b0d14]/95 backdrop-blur-xl border-r border-white/10 z-[60] transition-all duration-300 ease-in-out flex flex-col justify-between overflow-hidden shadow-2xl font-sans"
>
    <!-- Top Brand & Header Section -->
    <div class="p-3.5 border-b border-white/10 flex items-center min-h-[64px]" :class="sidebarOpen ? 'justify-between' : 'justify-center'">
        <!-- Logo (Visible when Sidebar is Open) -->
        <a href="{{ url('/') }}" x-show="sidebarOpen" x-transition.opacity.duration.200ms class="flex items-center gap-3 overflow-hidden">
            <div class="w-9 h-9 rounded-xl bg-neon-gradient flex items-center justify-center text-white font-black text-base shrink-0 shadow-lg shadow-[#ff5b00]/20">
                <i class="ri-flashlight-fill"></i>
            </div>
            <span class="text-base font-black uppercase tracking-wider text-white whitespace-nowrap">
                <span class="text-[#ff5b00]">FIT</span>CLUB
            </span>
        </a>

        <!-- Desktop Toggle Button inside Sidebar -->
        <button
            @click="sidebarOpen = !sidebarOpen; localStorage.setItem('fitclub_sidebar_open', sidebarOpen)"
            type="button"
            class="hidden md:flex w-9 h-9 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white items-center justify-center transition cursor-pointer shrink-0 border border-white/10 hover:border-[#ff5b00]/40"
            :title="sidebarOpen ? 'Collapse Sidebar' : 'Expand Sidebar'"
        >
            <i :class="sidebarOpen ? 'ri-menu-fold-line text-lg text-[#ff5b00]' : 'ri-menu-unfold-line text-lg text-[#ff5b00]'"></i>
        </button>

        <!-- Mobile Close Button -->
        <button
            @click="mobileSidebarOpen = false"
            type="button"
            class="md:hidden w-8 h-8 rounded-lg bg-white/5 text-gray-400 hover:text-white flex items-center justify-center transition cursor-pointer"
        >
            <i class="ri-close-line text-lg"></i>
        </button>
    </div>

    <!-- Navigation Scrollable Area -->
    <div class="flex-1 overflow-y-auto overflow-x-hidden py-4 px-3 space-y-6 custom-scrollbar">

        <!-- 1. MAIN NAVIGATION -->
        <div class="space-y-1">
            <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="px-3 text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-2">
                Main Overview
            </span>

            <!-- Dashboard -->
            <a
                href="{{ route('dashboard') }}"
                @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('dashboard') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
            >
                <i class="ri-dashboard-3-line text-lg shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-[#ff5b00]' }}"></i>
                <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Dashboard</span>

                <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                    Dashboard
                </span>
            </a>

            <!-- Subscriptions / Plans & Schedules (Members only) -->
            @role('member')
                <a
                    href="{{ route('plans.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('plans.*', 'schedules.*', 'subscription.plans*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-price-tag-3-line text-lg shrink-0 {{ request()->routeIs('plans.*', 'schedules.*', 'subscription.plans*') ? 'text-white' : 'text-amber-400' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Plans & Schedules</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Plans & Schedules
                    </span>
                </a>

                <!-- Private Trainer Bookings -->
                <a
                    href="{{ route('bookings.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('bookings.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-user-star-line text-lg shrink-0 {{ request()->routeIs('bookings.*') ? 'text-white' : 'text-purple-400' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Private Coaches</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Private Coaches
                    </span>
                </a>

                <!-- Workout Routine -->
                <a
                    href="{{ route('workout.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('workout.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-heart-pulse-line text-lg shrink-0 {{ request()->routeIs('workout.*') ? 'text-white' : 'text-pink-400' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Workout Routine</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Workout Routine
                    </span>
                </a>

                <!-- Nutrition Diet Plan -->
                <a
                    href="{{ route('diet.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('diet.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-restaurant-2-line text-lg shrink-0 {{ request()->routeIs('diet.*') ? 'text-white' : 'text-emerald-400' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Nutrition & Diet</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Nutrition & Diet
                    </span>
                </a>
            @endrole

            <!-- Live Chat -->
            <a
                href="{{ route('chat.index') }}"
                @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('chat.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
            >
                <i class="ri-chat-smile-2-line text-lg shrink-0 {{ request()->routeIs('chat.*') ? 'text-white' : 'text-emerald-400' }}"></i>
                <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate flex-1 flex items-center justify-between">
                    <span>Live Chat</span>
                    <span x-show="$store.unreadChat && $store.unreadChat.count > 0" x-text="$store.unreadChat.count" class="px-2 py-0.5 text-[10px] font-black rounded-full bg-[#ff5b00] text-white shadow-md shadow-[#ff5b00]/40 animate-pulse"></span>
                </span>

                <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                    Live Chat
                </span>
            </a>
        </div>

        <!-- 2. ATHLETES & TRAINING (Trainer Only) -->
        @role('trainer')
            <div class="space-y-1 pt-3 border-t border-white/5">
                <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="px-3 text-[10px] font-black uppercase tracking-widest text-[#ff5b00] block mb-2">
                    Athletes & Training
                </span>

                <!-- My Athletes Roster -->
                <a
                    href="{{ route('trainer.members.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('trainer.members.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-user-heart-line text-lg shrink-0 {{ request()->routeIs('trainer.members.*') ? 'text-white' : 'text-[#ff5b00]' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">My Athletes Roster</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        My Athletes Roster
                    </span>
                </a>

                <!-- Earnings Wallet -->
                <a
                    href="{{ route('wallet.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('wallet.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-wallet-3-line text-lg shrink-0 {{ request()->routeIs('wallet.*') ? 'text-white' : 'text-emerald-400' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Earnings Wallet</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Earnings Wallet
                    </span>
                </a>
            </div>
        @endrole

        <!-- 3. MASTER ADMINISTRATION (Admin Only) -->
        @role('admin')
            <div class="space-y-1 pt-3 border-t border-white/5">
                <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="px-3 text-[10px] font-black uppercase tracking-widest text-red-500 block mb-2">
                    Master Admin Control
                </span>

                <!-- Users & Security -->
                <a
                    href="{{ route('admin.users.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('admin.users.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-shield-user-line text-lg shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-red-400' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Users & Moderation</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Users & Moderation
                    </span>
                </a>

                <!-- Trainers & Recruitment -->
                <a
                    href="{{ route('admin.trainers.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('admin.trainers.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-user-star-line text-lg shrink-0 {{ request()->routeIs('admin.trainers.*') ? 'text-white' : 'text-[#ff5b00]' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Trainers & Recruitment</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Trainers & Recruitment
                    </span>
                </a>

                <!-- Coaches Workouts & Diets Oversight -->
                <a
                    href="{{ route('trainer.members.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('trainer.members.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-user-heart-line text-lg shrink-0 {{ request()->routeIs('trainer.members.*') ? 'text-white' : 'text-pink-400' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Coaches Plans Oversight</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Coaches Plans Oversight
                    </span>
                </a>

                <!-- Gym Rules Management -->
                <a
                    href="{{ route('admin.rules.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('admin.rules.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-file-shield-line text-lg shrink-0 {{ request()->routeIs('admin.rules.*') ? 'text-white' : 'text-amber-400' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Gym Rules</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Gym Rules
                    </span>
                </a>

                <!-- Admin Plans -->
                <a
                    href="{{ route('admin.plans.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('admin.plans.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-shield-star-line text-lg shrink-0 {{ request()->routeIs('admin.plans.*') ? 'text-white' : 'text-yellow-400' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Admin Plans</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Admin Plans
                    </span>
                </a>

                <!-- Admin Schedules -->
                <a
                    href="{{ route('admin.schedules.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('admin.schedules.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-time-line text-lg shrink-0 {{ request()->routeIs('admin.schedules.*') ? 'text-white' : 'text-sky-400' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Admin Schedules</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Admin Schedules
                    </span>
                </a>

                <!-- Admin Bookings -->
                <a
                    href="{{ route('admin.bookings.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('admin.bookings.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-calendar-check-line text-lg shrink-0 {{ request()->routeIs('admin.bookings.*') ? 'text-white' : 'text-purple-400' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Admin Bookings</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Admin Bookings
                    </span>
                </a>

                <!-- Master Ledger -->
                <a
                    href="{{ route('admin.payments.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('admin.payments.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-money-dollar-circle-line text-lg shrink-0 {{ request()->routeIs('admin.payments.*') ? 'text-white' : 'text-emerald-400' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Master Ledger</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Master Ledger
                    </span>
                </a>

                <!-- Trainers Treasury & Payouts -->
                <a
                    href="{{ route('admin.payouts.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('admin.payouts.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-bank-card-line text-lg shrink-0 {{ request()->routeIs('admin.payouts.*') ? 'text-white' : 'text-indigo-400' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Trainers Treasury & Wallets</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Trainers Treasury & Wallets
                    </span>
                </a>

                <!-- Security & Activity Audit Logs -->
                <a
                    href="{{ route('admin.activity-logs.index') }}"
                    @click="sidebarOpen = false; localStorage.setItem('fitclub_sidebar_open', 'false'); mobileSidebarOpen = false;"
                    :class="sidebarOpen ? 'px-3.5 py-2.5' : 'justify-center p-2.5'"
                    class="flex items-center gap-3.5 rounded-xl text-xs font-bold transition-all duration-200 group relative {{ request()->routeIs('admin.activity-logs.*') ? 'bg-neon-gradient text-white shadow-lg shadow-[#ff5b00]/30 font-black' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                >
                    <i class="ri-shield-check-line text-lg shrink-0 {{ request()->routeIs('admin.activity-logs.*') ? 'text-white' : 'text-emerald-400' }}"></i>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="truncate">Audit Logs & Security</span>

                    <span x-show="!sidebarOpen" class="fixed left-20 bg-[#181a24] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-[70]">
                        Audit Logs & Security
                    </span>
                </a>
            </div>
        @endrole
    </div>

    <!-- Bottom User Profile Card inside Sidebar -->
    @auth
        <div class="p-3 border-t border-white/10 bg-white/[0.02] flex items-center" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            @if(Auth::user()->hasMedia('avatar'))
                <img src="{{ Auth::user()->getFirstMediaUrl('avatar', 'thumb') ?: Auth::user()->getFirstMediaUrl('avatar') }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-xl object-cover border border-[#ff5b00]/50 shrink-0" :title="Auth::user()->name">
            @else
                <div class="w-9 h-9 rounded-xl bg-[#181a28] border border-white/10 flex items-center justify-center text-[#ff5b00] font-black uppercase shrink-0" :title="Auth::user()->name">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            @endif
            <div x-show="sidebarOpen" x-transition.opacity.duration.200ms class="min-w-0 flex-1 ml-3">
                <p class="text-xs font-black text-white truncate">{{ Auth::user()->name }}</p>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block truncate">
                    @if(Auth::user()->hasRole('admin'))
                        🛡️ Master Admin
                    @elseif(Auth::user()->hasRole('trainer'))
                        🏋️ Certified Coach
                    @else
                        👤 Platform Member
                    @endif
                </span>
            </div>
        </div>
    @else
        <div class="p-3 border-t border-white/10 bg-white/[0.02] flex items-center justify-center">
            <a href="{{ route('login') }}" class="text-xs text-[#ff5b00] font-bold hover:underline flex items-center gap-1.5">
                <i class="ri-login-box-line"></i>
                <span x-show="sidebarOpen">Log In</span>
            </a>
        </div>
    @endauth
</aside>

<!-- Backdrop Blur Overlay for Mobile Drawer Mode -->
<div
    x-show="mobileSidebarOpen"
    @click="mobileSidebarOpen = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[55] bg-black/80 backdrop-blur-sm md:hidden"
></div>
