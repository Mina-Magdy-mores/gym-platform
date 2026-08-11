<x-app-layout>
    <x-slot name="title">Admin Bookings Ledger</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-xl text-white leading-tight uppercase tracking-wider flex items-center gap-2">
                    <i class="ri-shield-user-line text-[#ff5b00]"></i> Master Admin Bookings Control Panel
                </h2>
                <p class="text-xs text-gray-400 mt-1">Full platform ledger of member session bookings, trainer assignments, and payment statuses.</p>
            </div>
            <div class="px-3 py-1.5 bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 rounded-xl text-xs font-black uppercase">
                Total Bookings: {{ $bookings->count() }}
            </div>
        </div>
    </x-slot>

    <div class="bg-[#0f172a]/80 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-6">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs text-gray-300">
                <thead class="bg-white/5 text-gray-400 uppercase tracking-wider font-bold border-b border-white/10">
                    <tr>
                        <th class="py-3 px-4 text-center">#ID</th>
                        <th class="py-3 px-4">Member Info</th>
                        <th class="py-3 px-4">Trainer</th>
                        <th class="py-3 px-4">Date & Time</th>
                        <th class="py-3 px-4 text-center">Price / Payment</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Refund Status</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 font-medium">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-white/[0.03] transition duration-150">
                            <td class="py-4 px-4 text-center font-mono font-bold text-white">#{{ $booking->id }}</td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-white text-sm">{{ $booking->user->name ?? 'N/A' }}</div>
                                <div class="text-gray-400 text-[11px]">{{ $booking->user->email ?? '' }}</div>
                                <div class="text-[10px] text-[#ff5b00] font-mono mt-0.5">
                                    Plan: {{ $booking->user->activeSubscription->plan->name ?? 'No Active Plan' }}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-200">{{ $booking->trainer->name ?? 'Unassigned' }}</div>
                                <div class="text-gray-400 text-[11px]">{{ $booking->trainer->email ?? '' }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-white">{{ $booking->booking_date }}</div>
                                <div class="text-gray-400 text-[11px] font-mono">{{ $booking->start_time }} - {{ $booking->end_time }}</div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($booking->price == 0)
                                    <span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-full font-bold">
                                        Subscription Benefit
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-amber-500/20 text-amber-400 border border-amber-500/30 rounded-full font-bold">
                                        EGP {{ number_format($booking->price, 2) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                @switch($booking->status)
                                    @case('confirmed')
                                        <span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-full font-bold">Confirmed</span>
                                        @break
                                    @case('completed')
                                        <span class="px-2.5 py-1 bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-full font-bold">Completed</span>
                                        @break
                                    @case('cancelled')
                                        <span class="px-2.5 py-1 bg-rose-500/20 text-rose-400 border border-rose-500/30 rounded-full font-bold">Cancelled</span>
                                        @break
                                    @default
                                        <span class="px-2.5 py-1 bg-gray-500/20 text-gray-400 border border-gray-500/30 rounded-full font-bold">{{ ucfirst($booking->status) }}</span>
                                @endswitch
                            </td>
                            <td class="py-4 px-4 text-center">
                                @switch($booking->refund_status)
                                    @case('refunded')
                                        <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-full text-[10px] font-bold">Restored/Refunded</span>
                                        @break
                                    @case('pending')
                                        <span class="px-2 py-0.5 bg-amber-500/20 text-amber-400 border border-amber-500/30 rounded-full text-[10px] font-bold">Pending Refund</span>
                                        @break
                                    @default
                                        <span class="text-gray-500 text-[10px]">-</span>
                                @endswitch
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($booking->status === 'confirmed')
                                        <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1 bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white rounded-lg transition text-[11px] font-bold">
                                                Cancel
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-500 text-[11px]">No Actions</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-500">
                                No session bookings recorded in the system yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
