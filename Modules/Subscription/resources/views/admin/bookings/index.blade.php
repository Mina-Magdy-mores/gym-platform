<x-app-layout>
    <x-slot name="title">Admin Bookings Control Panel</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                    <i class="ri-calendar-check-line text-[#ff5b00]"></i> Master Admin Bookings Control Panel
                </h2>
                <p class="text-xs text-gray-400 mt-1">Full platform ledger of member session bookings, trainer assignments, and payment statuses</p>
            </div>
            <div class="px-3.5 py-1.5 bg-[#ff5b00]/10 text-[#ff5b00] border border-[#ff5b00]/30 rounded-xl text-xs font-black uppercase">
                Total Bookings: {{ $stats['total'] ?? $bookings->total() }}
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ activeRefundId: null, activePrice: 0 }">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-green-500/20 border border-green-500/30 text-green-400 font-bold text-sm flex items-center gap-2">
                <i class="ri-checkbox-circle-fill text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-500/20 border border-red-500/30 text-red-400 font-bold text-sm flex items-center gap-2">
                <i class="ri-error-warning-fill text-xl"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Quick Telemetry KPI Stats Grid (4 Cards) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-[#12141c]/90 border border-white/10 space-y-1">
                <div class="text-[10px] text-gray-400 uppercase font-black tracking-wider">Total Bookings</div>
                <div class="text-2xl font-black text-white font-mono">{{ $stats['total'] ?? 0 }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-[#12141c]/90 border border-green-500/20 space-y-1">
                <div class="text-[10px] text-green-400 uppercase font-black tracking-wider">Confirmed & Upcoming</div>
                <div class="text-2xl font-black text-green-400 font-mono">{{ $stats['confirmed'] ?? 0 }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-[#12141c]/90 border border-blue-500/20 space-y-1">
                <div class="text-[10px] text-blue-400 uppercase font-black tracking-wider">Completed Sessions</div>
                <div class="text-2xl font-black text-blue-400 font-mono">{{ $stats['completed'] ?? 0 }}</div>
            </div>
            <div class="p-4 rounded-2xl bg-[#12141c]/90 border border-red-500/20 space-y-1">
                <div class="text-[10px] text-red-400 uppercase font-black tracking-wider">Cancelled Sessions</div>
                <div class="text-2xl font-black text-red-400 font-mono">{{ $stats['cancelled'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-5 shadow-2xl">
            <form method="GET" action="{{ route('admin.bookings.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                <div class="md:col-span-5 relative w-full">
                    <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by ID, member name, or trainer..."
                        class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs pl-10 pr-4 py-2.5 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]"
                    >
                </div>

                <div class="md:col-span-3 w-full">
                    <select name="trainer_id" onchange="this.form.submit()" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs px-3 py-2.5 focus:border-[#ff5b00]">
                        <option value="">-- All Coaches --</option>
                        @foreach($trainers as $t)
                            <option value="{{ $t->id }}" {{ request('trainer_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2 w-full">
                    <select name="status" onchange="this.form.submit()" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs px-3 py-2.5 focus:border-[#ff5b00]">
                        <option value="">-- All Statuses --</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="md:col-span-2 w-full">
                    <button type="submit" class="w-full py-2.5 bg-[#ff5b00] hover:bg-[#ff5b00]/90 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg transition cursor-pointer">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Bookings Master Table -->
        <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-white/10 text-gray-400 uppercase font-black tracking-wider">
                            <th class="py-3 px-4">Booking ID</th>
                            <th class="py-3 px-4">Member Info</th>
                            <th class="py-3 px-4">Assigned Coach</th>
                            <th class="py-3 px-4">Session Date & Time</th>
                            <th class="py-3 px-4">Fee / Billing</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Refund Resolution</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 font-semibold text-gray-300">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-4 px-4 font-mono font-bold text-white">#{{ $booking->id }}</td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        @if($booking->user?->getFirstMediaUrl('avatar', 'thumb'))
                                            <img src="{{ $booking->user->getFirstMediaUrl('avatar', 'thumb') }}" alt="Avatar" class="w-8 h-8 rounded-lg object-cover border border-[#ff5b00] shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-lg bg-neon-gradient flex items-center justify-center font-black text-white text-xs shrink-0">
                                                {{ strtoupper(substr($booking->user?->name ?? 'M', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="font-bold text-white text-sm truncate">{{ $booking->user->name ?? 'N/A' }}</div>
                                            <div class="text-gray-400 text-[11px] truncate">{{ $booking->user->email ?? '' }}</div>
                                            <div class="text-[10px] text-[#ff5b00] font-mono mt-0.5 uppercase">
                                                Plan: {{ $booking->user->activeSubscription->plan->name ?? 'No Active Plan' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-white text-sm">{{ $booking->trainer->name ?? 'Unassigned' }}</div>
                                    <div class="text-gray-400 text-[11px]">{{ $booking->trainer->email ?? '' }}</div>
                                </td>
                                <td class="py-4 px-4 text-gray-300">
                                    <div class="font-bold text-white">{{ $booking->booking_date }}</div>
                                    <div class="text-gray-400 text-[11px] font-mono">{{ $booking->start_time }} - {{ $booking->end_time }}</div>
                                </td>
                                <td class="py-4 px-4 font-black text-sm">
                                    @if($booking->price == 0)
                                        <span class="text-green-400">0.00 EGP</span>
                                        <div class="text-[10px] text-gray-400 font-normal">Subscription Pass</div>
                                    @else
                                        <span class="text-green-400">{{ number_format($booking->price, 2) }} EGP</span>
                                        <div class="text-[10px] text-gray-400 font-normal">Out-of-Pocket</div>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($booking->status === 'confirmed')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-green-500/20 text-green-400 border border-green-500/30">Confirmed</span>
                                    @elseif($booking->status === 'completed')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30">Completed</span>
                                    @elseif($booking->status === 'cancelled')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30">Cancelled</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-gray-500/20 text-gray-400 border border-gray-500/30">{{ ucfirst($booking->status) }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($booking->refund_status === 'refunded')
                                        <div>
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-green-500/20 text-green-400 border border-green-500/30">Restored / Refunded</span>
                                            <div class="text-[10px] text-gray-400 font-mono mt-1">Via: {{ $booking->refund_method ?? 'System' }}</div>
                                        </div>
                                    @elseif($booking->refund_status === 'pending')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Pending Refund</span>
                                    @else
                                        <span class="text-gray-500 text-xs italic">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($booking->refund_status === 'pending')
                                            <div x-data="{ openRefund: false }" class="relative inline-block text-left" :class="{ 'z-50': openRefund }">
                                                <button @click="openRefund = !openRefund" type="button" class="px-3 py-1.5 rounded-lg bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 hover:bg-[#ff5b00] hover:text-white font-black uppercase text-[10px] transition inline-flex items-center gap-1 cursor-pointer">
                                                    <i class="ri-refund-2-line"></i> Resolve Refund
                                                </button>

                                                <!-- Popover Menu -->
                                                <div x-show="openRefund"
                                                     @click.away="openRefund = false"
                                                     x-transition:enter="transition ease-out duration-150"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-100"
                                                     x-transition:leave-start="opacity-100 scale-100"
                                                     x-transition:leave-end="opacity-0 scale-95"
                                                     class="origin-top-right absolute right-0 mt-2 w-80 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.95)] border border-white/20 p-4 z-[9999] space-y-3.5 text-left font-sans"
                                                     style="display: none; background-color: #12141c;">
                                                    
                                                    <div class="flex items-center justify-between border-b border-white/10 pb-3">
                                                        <div>
                                                            <h3 class="text-xs font-black text-white uppercase tracking-wider flex items-center gap-1.5">
                                                                <i class="ri-refund-2-line text-[#ff5b00]"></i> Issue Refund
                                                            </h3>
                                                            <p class="text-[10px] text-gray-400">Booking #{{ $booking->id }} &bull; {{ $booking->user->name ?? 'Member' }}</p>
                                                        </div>
                                                        <span class="text-xs font-black text-green-400 font-mono">{{ number_format($booking->price, 2) }} EGP</span>
                                                    </div>

                                                    <form action="{{ route('admin.bookings.refund', $booking->id) }}" method="POST" class="space-y-3">
                                                        @csrf
                                                        <div class="space-y-1">
                                                            <label class="block text-[10px] font-black text-gray-300 uppercase tracking-wider">Resolution Gateway</label>
                                                            <select name="refund_method" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-2.5 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]">
                                                                <option value="auto_gateway">⚡ Auto Gateway Refund (Stripe / Paymob)</option>
                                                                <option value="instapay">📲 InstaPay Transfer</option>
                                                                <option value="vodafone_cash">📱 Vodafone Cash</option>
                                                                <option value="in_gym_cash">💵 In-Gym Cash</option>
                                                            </select>
                                                        </div>

                                                        <div class="flex items-center gap-2 pt-1">
                                                            <button type="button" @click="openRefund = false" class="w-1/2 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-black text-[10px] uppercase tracking-wider transition cursor-pointer">
                                                                Cancel
                                                            </button>
                                                            <button type="submit" class="w-1/2 py-2 rounded-xl bg-[#ff5b00] hover:bg-[#ff5b00]/90 text-white font-black text-[10px] uppercase tracking-wider transition shadow-lg cursor-pointer">
                                                                Confirm Refund
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif

                                        @if($booking->user_id)
                                            <a
                                                href="{{ route('chat.show', $booking->user_id) }}"
                                                class="px-2.5 py-1.5 rounded-lg bg-white/5 hover:bg-white/15 text-gray-300 hover:text-white text-[10px] font-bold transition flex items-center gap-1 border border-white/10"
                                                title="Chat with Member"
                                            >
                                                <i class="ri-chat-smile-2-line text-[#ff5b00]"></i> Member
                                            </a>
                                        @endif

                                        @if($booking->trainer_id)
                                            <a
                                                href="{{ route('chat.show', $booking->trainer_id) }}"
                                                class="px-2.5 py-1.5 rounded-lg bg-white/5 hover:bg-white/15 text-gray-300 hover:text-white text-[10px] font-bold transition flex items-center gap-1 border border-white/10"
                                                title="Chat with Coach"
                                            >
                                                <i class="ri-chat-smile-2-line text-sky-400"></i> Coach
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-400 italic">No bookings match the selected criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($bookings->hasPages())
                <div class="pt-4 border-t border-white/10">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
