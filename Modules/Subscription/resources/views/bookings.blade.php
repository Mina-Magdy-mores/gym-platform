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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Book Session Form (1 Column) -->
                <div class="lg:col-span-1 glass-card p-6 rounded-2xl border border-white/5 space-y-6">
                    <div class="space-y-1 pb-4 border-b border-white/10">
                        <h3 class="text-xl font-bold text-white uppercase">Book a Session</h3>
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

                        <button type="submit" class="w-full py-3 rounded-full bg-neon-gradient text-white font-bold hover:opacity-90 transition shadow-lg text-sm bg-neon-glow cursor-pointer mt-2">
                            Confirm Booking
                        </button>
                    </form>
                </div>

                <!-- Bookings List (2 Columns) -->
                <div class="lg:col-span-2 glass-card p-6 rounded-2xl border border-white/5 space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-white/10">
                        <h3 class="text-xl font-bold text-white uppercase">Your Confirmed Sessions</h3>
                        <span class="text-xs text-gray-400 font-bold">{{ $bookings->count() }} Sessions</span>
                    </div>

                    @if($bookings->count() > 0)
                        <div class="space-y-3">
                            @foreach($bookings as $bk)
                                <div class="p-4 rounded-xl bg-white/5 border border-white/5 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-neon-gradient flex items-center justify-center font-black text-white text-lg">
                                            <i class="ri-user-star-line"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-white text-sm">
                                                {{ str_starts_with($bk->trainer?->name ?? '', 'Captain') ? $bk->trainer->name : 'Captain ' . ($bk->trainer?->name ?? 'Trainer') }}
                                            </div>
                                            <div class="text-xs text-gray-400 flex items-center gap-2 mt-0.5">
                                                <span><i class="ri-calendar-line"></i> {{ $bk->booking_date }}</span>
                                                <span><i class="ri-time-line"></i> {{ $bk->start_time }} - {{ $bk->end_time }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right flex items-center gap-2">
                                        @if($bk->payment)
                                            <a href="{{ route('invoices.show', $bk->payment->id) }}" target="_blank" class="px-2 py-1 rounded-lg bg-white/10 text-white hover:bg-white/20 transition text-[10px] font-bold inline-flex items-center gap-1">
                                                <i class="ri-eye-line"></i> View
                                            </a>
                                            <a href="{{ route('invoices.download', $bk->payment->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 hover:bg-[#ff5b00] hover:text-white transition text-[10px] font-bold inline-flex items-center gap-1">
                                                <i class="ri-download-2-line"></i> PDF
                                            </a>
                                        @else
                                            <span class="px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[10px] font-bold inline-flex items-center gap-1">
                                                <i class="ri-vip-crown-2-line"></i> Covered by Plan
                                            </span>
                                        @endif
                                        <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-400 text-xs font-bold uppercase tracking-wider">
                                            {{ $bk->status }}
                                        </span>
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
