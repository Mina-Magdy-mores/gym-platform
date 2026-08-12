<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                <span class="neon-accent">Private Trainer</span> Bookings
            </h2>
            <div class="text-xs text-gray-400 font-bold uppercase tracking-widest">
                1-on-1 Personal Training
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Flash Alerts -->
            @if(session('status') === 'booked')
                <div class="p-4 rounded-xl glass-card border border-green-500/30 text-green-400 font-bold text-sm flex items-center gap-2">
                    <i class="ri-checkbox-circle-fill text-xl"></i>
                    <span>Trainer session booked successfully with race-condition locking protection!</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl glass-card border border-red-500/30 text-red-400 font-bold text-sm flex items-center gap-2">
                    <i class="ri-error-warning-fill text-xl"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Book Session Form (4 Columns - Compact Side Display) -->
                <div class="lg:col-span-4 glass-card p-6 rounded-2xl border border-white/5 space-y-6">
                    <div class="space-y-1 pb-4 border-b border-white/10">
                        <h3 class="text-xl font-bold text-white uppercase flex items-center gap-2">
                            <i class="ri-calendar-check-line neon-accent"></i> Book a Session
                        </h3>
                        <p class="text-xs text-gray-400">Select your preferred trainer, date, and time slot.</p>
                    </div>

                    <form method="POST" action="{{ route('bookings.store') }}" class="space-y-4">
                        @csrf

                        <!-- Select Trainer -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Select Trainer</label>
                            <select name="trainer_id" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                                <option value="" class="bg-[#181a20] text-gray-400">-- Choose Captain --</option>
                                @foreach($trainers as $tr)
                                    <option value="{{ $tr->id }}" class="bg-[#181a20] text-white">{{ $tr->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-1" :messages="$errors->get('trainer_id')" />
                        </div>

                        <!-- Booking Date -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Booking Date</label>
                            <input type="date" name="booking_date" min="{{ date('Y-m-d') }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                            <x-input-error class="mt-1" :messages="$errors->get('booking_date')" />
                        </div>

                        <!-- Start & End Time -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-300">Start Time</label>
                                <input type="time" name="start_time" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                                <x-input-error class="mt-1" :messages="$errors->get('start_time')" />
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-300">End Time</label>
                                <input type="time" name="end_time" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                                <x-input-error class="mt-1" :messages="$errors->get('end_time')" />
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Special Requests / Notes</label>
                            <textarea name="notes" rows="2" placeholder="e.g. Focus on chest & triceps workout" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]"></textarea>
                        </div>

                        <!-- Payment Gateway Selector (If Out-of-pocket Payment Required) -->
                        <div class="space-y-2 pt-2 border-t border-white/10" x-data="{ selectedGateway: 'paymob' }">
                            <label class="text-[11px] font-bold text-gray-300 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="ri-bank-card-2-line neon-accent"></i> Payment Method (Out-of-Pocket)
                            </label>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <label class="p-2.5 rounded-xl border cursor-pointer transition flex items-center justify-between"
                                    :class="selectedGateway === 'paymob' ? 'bg-[#ff5b00]/10 border-[#ff5b00] text-white' : 'bg-white/5 border-white/10 text-gray-400 hover:border-white/20'">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="gateway" value="paymob" x-model="selectedGateway" class="text-[#ff5b00] focus:ring-[#ff5b00]">
                                        <span class="font-bold text-xs">Paymob</span>
                                    </div>
                                    <i class="ri-bank-card-line text-sm text-[#ff5b00]"></i>
                                </label>

                                <label class="p-2.5 rounded-xl border cursor-pointer transition flex items-center justify-between"
                                    :class="selectedGateway === 'stripe' ? 'bg-blue-500/10 border-blue-500 text-white' : 'bg-white/5 border-white/10 text-gray-400 hover:border-white/20'">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="gateway" value="stripe" x-model="selectedGateway" class="text-blue-500 focus:ring-blue-500">
                                        <span class="font-bold text-xs">Stripe</span>
                                    </div>
                                    <i class="ri-visa-line text-sm text-blue-400"></i>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 rounded-full bg-neon-gradient text-white font-bold hover:opacity-90 transition shadow-lg text-sm bg-neon-glow cursor-pointer mt-2">
                            Confirm Booking
                        </button>
                    </form>
                </div>

                <!-- Bookings List (8 Columns - Full Wide Display) -->
                <div class="lg:col-span-8 glass-card p-6 sm:p-8 rounded-2xl border border-white/5 space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-white/10">
                        <div>
                            <h3 class="text-xl font-bold text-white uppercase flex items-center gap-2">
                                <i class="ri-user-star-line neon-accent"></i> Your Confirmed Sessions
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Upcoming 1-on-1 personal training reservations</p>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-white/10 text-white text-xs font-black">
                            {{ $bookings->count() }} Sessions
                        </span>
                    </div>

                    @if($bookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($bookings as $bk)
                                @php
                                    $rawName = $bk->trainer?->name ?? 'Trainer';
                                    $cleanName = preg_replace('/^(Captain|Coach)\s+/i', '', $rawName);
                                @endphp
                                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:border-[#ff5b00]/40 transition shadow-xl">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-neon-gradient flex items-center justify-center font-black text-white text-xl shrink-0 shadow-lg">
                                            <i class="ri-user-star-line"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="font-black text-white text-base tracking-wide">
                                                Captain {{ $cleanName }}
                                            </div>
                                            <div class="text-xs text-gray-300 flex flex-wrap items-center gap-4 font-mono">
                                                <span class="inline-flex items-center gap-1"><i class="ri-calendar-line text-[#ff5b00]"></i> {{ $bk->booking_date }}</span>
                                                <span class="inline-flex items-center gap-1"><i class="ri-time-line text-[#ff5b00]"></i> {{ $bk->start_time }} - {{ $bk->end_time }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-3 shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-white/10 justify-end">
                                        @if($bk->payment)
                                            <a href="{{ route('invoices.show', $bk->payment->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white/10 text-white hover:bg-white/20 transition text-xs font-bold inline-flex items-center gap-1.5">
                                                <i class="ri-eye-line"></i> View
                                            </a>
                                        @else
                                            <span class="px-3 py-1.5 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20 text-xs font-bold inline-flex items-center gap-1.5">
                                                <i class="ri-vip-crown-2-line"></i> Covered by Plan
                                            </span>
                                        @endif

                                        @switch($bk->status)
                                            @case('confirmed')
                                                <span class="px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                                    Confirmed
                                                </span>
                                                @break
                                            @case('completed')
                                                <span class="px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                                    Completed
                                                </span>
                                                @break
                                            @case('cancelled')
                                                <span class="px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                                    Cancelled
                                                </span>
                                                @break
                                            @default
                                                <span class="px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-gray-500/20 text-gray-400 border border-gray-500/30">
                                                    {{ ucfirst($bk->status) }}
                                                </span>
                                        @endswitch

                                        @if($bk->status === 'confirmed')
                                            <!-- Trainer Mark as Completed Action -->
                                            @if(Auth::user()->id === $bk->trainer_id || Auth::user()->hasRole('admin'))
                                                <form action="{{ route('bookings.complete', $bk->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500 hover:text-white transition text-xs font-bold inline-flex items-center gap-1 cursor-pointer">
                                                        <i class="ri-checkbox-circle-line"></i> Complete
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Cancel Session Action -->
                                            <form action="{{ route('bookings.cancel', $bk->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this session?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-500/20 text-rose-300 hover:bg-rose-500 hover:text-white transition text-xs font-bold inline-flex items-center gap-1 cursor-pointer">
                                                    <i class="ri-close-circle-line"></i> Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-400 space-y-2">
                            <i class="ri-calendar-event-line text-4xl text-gray-500"></i>
                            <p class="text-sm font-bold">No booked sessions found yet.</p>
                            <p class="text-xs text-gray-500">Book your first 1-on-1 personal session using the form.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
