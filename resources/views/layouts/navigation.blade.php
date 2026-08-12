<nav class="glass-card border-b border-white/5 relative z-40 sticky top-0 backdrop-blur-xl bg-[#0b0d14]/80">
    <!-- Streamlined Header Navigation Bar -->
    <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-4">
                <!-- Hamburger Toggle Button (Desktop: Toggles Mini/Expanded Sidebar, Mobile: Opens Drawer) -->
                <button
                    @click.stop="if (window.innerWidth >= 768) { sidebarOpen = !sidebarOpen; localStorage.setItem('fitclub_sidebar_open', sidebarOpen); } else { mobileSidebarOpen = !mobileSidebarOpen; }"
                    type="button"
                    class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white transition cursor-pointer flex items-center justify-center border border-white/10 hover:border-[#ff5b00]/40"
                    title="Toggle cPanel Sidebar"
                >
                    <i class="ri-menu-2-line text-xl text-[#ff5b00]"></i>
                </button>

                <!-- Header Brand / Active Page Badge -->
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ url('/') }}" class="text-lg font-black uppercase tracking-wider hidden sm:block">
                        <span class="neon-accent">FIT</span><span class="text-white">CLUB</span>
                    </a>

                    <!-- Role Badge Indicator -->
                    <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-xs font-black uppercase text-gray-300 flex items-center gap-1.5">
                        @if(Auth::user()->hasRole('admin'))
                            <i class="ri-shield-user-line text-red-500"></i> Admin Panel
                        @elseif(Auth::user()->hasRole('trainer'))
                            <i class="ri-user-heart-line text-[#ff5b00]"></i> Coach Roster
                        @else
                            <i class="ri-user-smile-line text-emerald-400"></i> Member Portal
                        @endif
                    </span>
                </div>
            </div>

            <!-- Right Controls: Notifications & User Profile Dropdown -->
            <div class="flex items-center gap-3">
                <!-- Notifications Dropdown -->
                <div class="flex items-center">
                    <x-dropdown align="right" width="w-80" content-classes="py-0 bg-[#12141c] border border-white/15 shadow-[0_25px_60px_rgba(0,0,0,0.95)] rounded-2xl overflow-hidden">
                        <x-slot name="trigger">
                            <button class="relative p-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 hover:text-[#ff5b00] transition cursor-pointer border border-white/5">
                                <i class="ri-notification-3-line text-xl"></i>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-[#ff5b00] text-white text-[10px] font-black rounded-full flex items-center justify-center animate-pulse shadow-[0_0_10px_rgba(255,91,0,0.6)]">
                                        {{ Auth::user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-white/10 flex justify-between items-center bg-[#1a1d28]/80">
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

                            <div class="max-h-72 overflow-y-auto divide-y divide-white/5 bg-[#12141c]">
                                @forelse(Auth::user()->notifications()->latest()->take(6)->get() as $notification)
                                    <div class="p-3.5 hover:bg-white/5 transition duration-150 flex items-start justify-between gap-3 overflow-hidden {{ $notification->read_at ? 'bg-[#12141c] text-gray-300' : 'bg-white/[0.04] text-white' }}">
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
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-xs text-gray-400 font-bold uppercase tracking-wider">
                                        No new notifications
                                    </div>
                                @endforelse
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- User Profile Dropdown -->
                <div class="flex items-center">
                    <x-dropdown align="right" width="w-64" content-classes="py-0 bg-[#12141c] border border-white/15 shadow-[0_25px_60px_rgba(0,0,0,0.95)] rounded-2xl overflow-hidden">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-white/10 text-xs font-black rounded-xl text-white bg-white/5 hover:bg-white/10 hover:border-[#ff5b00]/40 focus:outline-none transition cursor-pointer gap-2">
                                @if(Auth::user()->hasMedia('avatar'))
                                    <img src="{{ Auth::user()->getFirstMediaUrl('avatar', 'thumb') ?: Auth::user()->getFirstMediaUrl('avatar') }}" alt="{{ Auth::user()->name }}" class="w-6 h-6 rounded-lg object-cover border border-[#ff5b00]/50 shrink-0">
                                @else
                                    <div class="w-6 h-6 rounded-lg bg-neon-gradient flex items-center justify-center text-white font-black text-xs uppercase shadow-md shrink-0">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                                <span class="hidden sm:inline-block max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                                <i class="ri-arrow-down-s-line text-sm text-gray-400"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-white/10 bg-white/[0.02] flex items-center gap-3">
                                @if(Auth::user()->hasMedia('avatar'))
                                    <img src="{{ Auth::user()->getFirstMediaUrl('avatar', 'thumb') ?: Auth::user()->getFirstMediaUrl('avatar') }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-lg object-cover border border-[#ff5b00]/50 shrink-0">
                                @else
                                    <div class="w-8 h-8 rounded-lg bg-neon-gradient flex items-center justify-center text-white font-black text-xs uppercase shadow-md shrink-0">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-black text-white truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[10px] text-gray-400 font-mono truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>

                            <div class="py-1">
                                @role('admin')
                                    <x-dropdown-link :href="route('admin.users.index')" class="text-xs font-bold text-red-400 hover:text-red-300 hover:bg-white/5">
                                        <i class="ri-shield-keyhole-line mr-1.5"></i> Security & Moderation
                                    </x-dropdown-link>
                                @endrole

                                @hasanyrole('trainer|admin')
                                    <x-dropdown-link :href="route('trainer.members.index')" class="text-xs font-bold text-[#ff5b00] hover:text-orange-300 hover:bg-white/5">
                                        <i class="ri-user-heart-line mr-1.5"></i> My Athletes Roster
                                    </x-dropdown-link>
                                @endhasanyrole

                                <x-dropdown-link :href="route('profile.edit')" class="text-xs font-bold text-gray-200 hover:text-white hover:bg-white/5">
                                    <i class="ri-user-settings-line mr-1.5"></i> {{ __('Profile Settings') }}
                                </x-dropdown-link>

                                <!-- Authentication Logout -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();"
                                            class="text-xs font-bold text-red-400 hover:text-red-300 hover:bg-white/5">
                                        <i class="ri-logout-box-r-line mr-1.5"></i> {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>
</nav>
