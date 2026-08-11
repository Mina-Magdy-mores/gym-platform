<nav x-data="{ open: false }" class="glass-card border-b border-white/5 relative z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-6">
                <!-- Logo Links to Welcome Landing Page -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="text-xl font-black uppercase tracking-wider">
                        <span class="neon-accent">FIT</span><span>CLUB</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:text-[#ff5b00]">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('plans.index')" :active="request()->routeIs('plans.index')" class="text-white hover:text-[#ff5b00]">
                        Plans & Schedules
                    </x-nav-link>
                    <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index')" class="text-white hover:text-[#ff5b00]">
                        Trainer Bookings
                    </x-nav-link>

                    @hasanyrole('trainer|admin')
                        <x-nav-link :href="route('wallet.index')" :active="request()->routeIs('wallet.*')" class="text-white hover:text-[#ff5b00]">
                            Earnings Wallet
                        </x-nav-link>
                    @endhasanyrole

                    @role('admin')
                        <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ request()->routeIs('admin.plans.*') ? 'bg-neon-gradient text-white shadow-lg bg-neon-glow' : 'text-[#ff5b00] border border-[#ff5b00]/40 hover:bg-[#ff5b00] hover:text-white' }} transition duration-200">
                            <i class="ri-shield-star-line text-sm"></i> Admin Plans
                        </a>
                        <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ request()->routeIs('admin.schedules.*') ? 'bg-neon-gradient text-white shadow-lg bg-neon-glow' : 'text-[#ff5b00] border border-[#ff5b00]/40 hover:bg-[#ff5b00] hover:text-white' }} transition duration-200">
                            <i class="ri-time-line text-sm"></i> Admin Schedules
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ request()->routeIs('admin.payments.*') ? 'bg-neon-gradient text-white shadow-lg bg-neon-glow' : 'text-[#ff5b00] border border-[#ff5b00]/40 hover:bg-[#ff5b00] hover:text-white' }} transition duration-200">
                            <i class="ri-money-dollar-circle-line text-sm"></i> Master Ledger
                        </a>
                        <a href="{{ route('admin.payouts.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ request()->routeIs('admin.payouts.*') ? 'bg-neon-gradient text-white shadow-lg bg-neon-glow' : 'text-[#ff5b00] border border-[#ff5b00]/40 hover:bg-[#ff5b00] hover:text-white' }} transition duration-200">
                            <i class="ri-bank-card-line text-sm"></i> Admin Payouts
                        </a>
                    @endrole
                </div>
            </div>

            <!-- Notifications Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-4">
                <x-dropdown align="right" width="w-80" content-classes="py-0 bg-[#0f172a] border border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.95)] rounded-2xl overflow-hidden shadow-2xl">
                    <x-slot name="trigger">
                        <button class="relative p-2 text-gray-300 hover:text-[#ff5b00] transition cursor-pointer">
                            <i class="ri-notification-3-line text-xl"></i>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <span class="absolute top-1 right-1 w-4 h-4 bg-[#ff5b00] text-white text-[10px] font-black rounded-full flex items-center justify-center animate-pulse shadow-[0_0_10px_rgba(255,91,0,0.6)]">
                                    {{ Auth::user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-white/10 flex justify-between items-center bg-[#0f172a]">
                            <span class="text-xs font-black uppercase text-white tracking-wider flex items-center gap-1.5">
                                <i class="ri-notification-3-line text-[#ff5b00]"></i> Notifications
                            </span>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-[#ff5b00] hover:text-white transition font-bold cursor-pointer uppercase tracking-wider">Mark all read</button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-72 overflow-y-auto divide-y divide-white/5 bg-[#0f172a]">
                            @forelse(Auth::user()->notifications()->latest()->take(6)->get() as $notification)
                                <div class="p-3.5 hover:bg-white/5 transition duration-150 flex items-start justify-between gap-3 overflow-hidden {{ $notification->read_at ? 'bg-[#0f172a] text-gray-300' : 'bg-white/[0.04] text-white' }}">
                                    <div class="flex-1 min-w-0 space-y-1">
                                        <div class="text-xs font-bold text-white flex items-center gap-1.5 flex-wrap break-words">
                                            @if(!$notification->read_at)
                                                <span class="w-2 h-2 rounded-full bg-[#ff5b00] inline-block shadow-[0_0_8px_#ff5b00] flex-shrink-0"></span>
                                            @endif
                                            <span class="break-words">{{ $notification->data['title'] ?? 'Notification' }}</span>
                                        </div>
                                        <div class="text-[11px] text-gray-300 leading-snug break-words font-medium">{{ $notification->data['message'] ?? '' }}</div>
                                        <div class="text-[9px] text-gray-400 font-mono flex items-center gap-1 pt-0.5">
                                            <i class="ri-time-line text-[10px]"></i> {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    @if(!$notification->read_at)
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-1 text-gray-400 hover:text-[#ff5b00] transition cursor-pointer" title="Mark as read">
                                                <i class="ri-check-double-line text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <div class="p-6 text-center text-xs text-gray-400 font-bold bg-[#0f172a]">No notifications found</div>
                            @endforelse
                        </div>

                        <div class="p-2 border-t border-white/10 bg-[#0f172a] text-center">
                            <a href="{{ route('notifications.index') }}" class="text-[11px] font-black text-[#ff5b00] hover:text-white uppercase tracking-wider block py-1 transition">
                                View All Notifications <i class="ri-arrow-right-s-line"></i>
                            </a>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-4">
                <x-dropdown align="right" width="48" content-classes="py-1 bg-[#0f172a] border border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.95)] rounded-2xl overflow-hidden opacity-100 z-50">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 px-3 py-2 border border-white/10 rounded-full text-sm font-bold text-white glass-card hover:border-[#ff5b00] transition duration-200 cursor-pointer">
                            @if(Auth::user()->getFirstMediaUrl('avatar', 'thumb'))
                                <img src="{{ Auth::user()->getFirstMediaUrl('avatar', 'thumb') }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-[#ff5b00]">
                            @else
                                <div class="w-8 h-8 rounded-full bg-neon-gradient flex items-center justify-center text-xs font-black">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif

                            <div>{{ Auth::user()->name }}</div>

                            <i class="ri-arrow-down-s-line text-lg"></i>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        @hasanyrole('trainer|admin')
                            <x-dropdown-link :href="route('wallet.index')">
                                Earnings Wallet
                            </x-dropdown-link>
                        @endhasanyrole

                        @role('admin')
                            <x-dropdown-link :href="route('admin.plans.index')">
                                Manage Subscription Plans
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.schedules.index')">
                                Manage Gym Schedules
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.payouts.index')">
                                Manage Trainer Payouts
                            </x-dropdown-link>
                        @endrole

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white focus:outline-none transition duration-150">
                    <i class="ri-menu-line text-2xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden glass-card border-t border-white/5">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('plans.index')" :active="request()->routeIs('plans.index')">
                Plans & Schedules
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index')">
                Trainer Bookings
            </x-responsive-nav-link>
            @role('admin')
                <x-responsive-nav-link :href="route('admin.plans.index')">
                    Admin Plans
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.schedules.index')">
                    Admin Schedules
                </x-responsive-nav-link>
            @endrole
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-white/10">
            <div class="px-4 flex items-center gap-3">
                @if(Auth::user()->getFirstMediaUrl('avatar', 'thumb'))
                    <img src="{{ Auth::user()->getFirstMediaUrl('avatar', 'thumb') }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover border border-[#ff5b00]">
                @else
                    <div class="w-10 h-10 rounded-full bg-neon-gradient flex items-center justify-center text-sm font-black">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <div class="font-bold text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
