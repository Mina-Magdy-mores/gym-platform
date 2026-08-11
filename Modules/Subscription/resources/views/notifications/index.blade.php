<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-xl text-white leading-tight uppercase tracking-wider flex items-center gap-2">
                <i class="ri-notification-3-line text-[#ff5b00]"></i> System Notifications
            </h2>
            @if(Auth::user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-neon-gradient hover:opacity-90 text-white text-xs font-black uppercase tracking-wider rounded-xl transition duration-200 shadow-md bg-neon-glow cursor-pointer">
                        Mark All As Read ({{ Auth::user()->unreadNotifications->count() }})
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-emerald-400 text-sm font-bold flex items-center gap-2">
                    <i class="ri-checkbox-circle-fill text-lg"></i> {{ session('success') }}
                </div>
            @endif

            <div class="glass-card rounded-2xl p-6 border border-white/5 shadow-2xl">
                <div class="space-y-3">
                    @forelse($notifications as $notification)
                        <div class="p-4 rounded-2xl border transition duration-200 flex items-center justify-between gap-4 overflow-hidden {{ $notification->read_at ? 'bg-white/[0.02] border-white/5 text-gray-200' : 'bg-white/[0.06] border-[#ff5b00]/40 shadow-[0_0_15px_rgba(255,91,0,0.1)] text-white' }}">
                            <div class="flex items-start gap-4 flex-1 min-w-0">
                                <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center text-lg {{ $notification->read_at ? 'bg-white/5 text-gray-300' : 'bg-neon-gradient text-white shadow-md' }}">
                                    @switch($notification->data['type'] ?? '')
                                        @case('session_reminder')
                                            <i class="ri-time-line"></i>
                                            @break
                                        @case('new_subscription')
                                            <i class="ri-vip-crown-line"></i>
                                            @break
                                        @case('payout_requested')
                                        @case('payout_approved')
                                            <i class="ri-wallet-3-line"></i>
                                            @break
                                        @default
                                            <i class="ri-notification-3-line"></i>
                                    @endswitch
                                </div>

                                <div class="space-y-1 flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm font-bold text-white break-words">{{ $notification->data['title'] ?? 'Notification' }}</h4>
                                        @if(!$notification->read_at)
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 flex-shrink-0">New</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-300 font-medium leading-relaxed break-words">{{ $notification->data['message'] ?? '' }}</p>
                                    <div class="text-[10px] text-gray-400 font-mono pt-1">
                                        <i class="ri-time-line text-xs"></i> {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            @if(!$notification->read_at)
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 bg-white/10 hover:bg-[#ff5b00] text-white text-xs font-bold rounded-xl transition duration-200 cursor-pointer flex items-center gap-1">
                                        <i class="ri-check-double-line"></i> Mark Read
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-16 space-y-3">
                            <div class="w-16 h-16 rounded-full bg-white/5 flex items-center justify-center mx-auto text-2xl text-gray-400">
                                <i class="ri-notification-off-line"></i>
                            </div>
                            <div class="text-gray-400 text-sm font-bold">No notifications found</div>
                            <p class="text-xs text-gray-500">All system alerts and reminders will appear here.</p>
                        </div>
                    @endforelse
                </div>

                @if($notifications->hasPages())
                    <div class="mt-6 pt-4 border-t border-white/10">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
