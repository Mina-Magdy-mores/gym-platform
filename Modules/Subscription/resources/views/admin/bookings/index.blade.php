<x-app-layout>
    <x-slot name="title">Admin Bookings Control Panel</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                    <i class="ri-shield-user-line text-[#ff5b00]"></i> Master Admin Bookings Control Panel
                </h2>
                <p class="text-xs text-gray-400 mt-1">Full platform ledger of member session bookings, trainer assignments, and payment statuses</p>
            </div>
            <div class="px-3 py-1.5 bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 rounded-xl text-xs font-black uppercase">
                Total Bookings: {{ $bookings->count() }}
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

        <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-white/10 text-gray-400 uppercase font-black tracking-wider">
                            <th class="py-3 px-4">Booking ID</th>
                            <th class="py-3 px-4">Member Info</th>
                            <th class="py-3 px-4">Trainer</th>
                            <th class="py-3 px-4">Date & Time</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Refund Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 font-semibold text-gray-300">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-4 px-4 font-mono font-bold text-white">#{{ $booking->id }}</td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-white text-sm">{{ $booking->user->name ?? 'N/A' }}</div>
                                    <div class="text-gray-400 text-[11px]">{{ $booking->user->email ?? '' }}</div>
                                    <div class="text-[10px] text-[#ff5b00] font-mono mt-0.5 uppercase">
                                        Plan: {{ $booking->user->activeSubscription->plan->name ?? 'No Active Plan' }}
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

                                                <!-- Popover Menu Directly Under the Button (No Full-Screen Backdrop) -->
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
                                        @elseif($booking->status === 'confirmed')
                                            <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500 hover:text-white font-black uppercase text-[10px] transition cursor-pointer">
                                                    Cancel
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-500 text-xs italic">Processed</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-400 italic">
                                    No session bookings recorded in the system yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
