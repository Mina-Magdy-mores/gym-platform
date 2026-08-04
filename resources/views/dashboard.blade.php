<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                <span class="neon-accent">Member</span> Dashboard
            </h2>
            <div class="px-4 py-1 rounded-full bg-neon-gradient text-white text-xs font-black uppercase tracking-widest bg-neon-glow">
                {{ Auth::user()->roles->first()?->name ?? 'Member' }}
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Header Card -->
            <div class="p-8 glass-card rounded-2xl border border-white/5 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-3 text-center md:text-left">
                    <div class="inline-block px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-gray-300">
                        Welcome Back, Champion!
                    </div>
                    <h3 class="text-3xl font-black uppercase tracking-wide">
                        Hello, <span class="neon-accent">{{ $user->name }}</span>
                    </h3>
                    <p class="text-gray-400 text-sm max-w-lg leading-relaxed">
                        Track your gym subscription benefits balance, view your remaining freeze days, and monitor your booked personal trainer sessions.
                    </p>
                    <div class="pt-2 flex flex-wrap items-center gap-4">
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-sm transition border border-white/10">
                            <span>Manage Profile</span> <i class="ri-user-settings-line"></i>
                        </a>
                        <a href="{{ url('/#plans') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-neon-gradient text-white font-bold text-sm bg-neon-glow hover:opacity-90 transition">
                            <span>Explore Plans</span> <i class="ri-fire-fill"></i>
                        </a>
                    </div>
                </div>

                <!-- Avatar Preview Badge -->
                <div class="shrink-0">
                    <div class="relative w-32 h-32 rounded-2xl overflow-hidden glass-card border border-[#ff5b00]/30 shadow-2xl flex items-center justify-center">
                        @if($user->getFirstMediaUrl('avatar'))
                            <img src="{{ $user->getFirstMediaUrl('avatar') }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <div class="text-4xl font-black text-white neon-accent">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Active Subscription Header Banner -->
            <div class="glass-card p-6 rounded-2xl border border-white/5 space-y-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-white/10 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="p-3 rounded-xl bg-neon-gradient text-white text-2xl shadow-lg">
                            <i class="ri-vip-crown-fill"></i>
                        </span>
                        <div>
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Current Membership</span>
                            <h4 class="text-xl font-black text-white uppercase tracking-wide">
                                {{ $activeSub ? ($activeSub->plan?->name ?? 'Custom Membership Plan') : 'No Active Membership Plan' }}
                            </h4>
                        </div>
                    </div>

                    @if($activeSub)
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-400 border border-green-500/30 text-xs font-black uppercase tracking-wider">
                                Active Until {{ $activeSub->ends_at->format('Y-m-d') }}
                            </span>
                            <span class="px-3 py-1 rounded-full bg-neon-gradient text-white text-xs font-black uppercase tracking-wider bg-neon-glow">
                                {{ (int) max(0, ceil(now()->diffInDays($activeSub->ends_at))) }} Days Left
                            </span>
                        </div>
                    @else
                        <a href="{{ url('/#plans') }}" class="px-5 py-2 rounded-full bg-neon-gradient text-white text-xs font-black uppercase tracking-wider bg-neon-glow hover:opacity-90 transition">
                            Subscribe Now
                        </a>
                    @endif
                </div>

                @if(!$activeSub)
                    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-between text-xs text-amber-300">
                        <div class="flex items-center gap-2">
                            <i class="ri-alert-line text-lg"></i>
                            <span>You do not have an active membership. Subscribe now to unlock full gym access and trainer sessions.</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Subscription Benefits Usage Balance Grid (6 Neon Glass Cards) -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-black text-white uppercase tracking-wide flex items-center gap-2">
                        <i class="ri-dashboard-3-line neon-accent"></i> Membership Benefits Balance
                    </h3>
                    <span class="text-xs text-gray-400 font-semibold">Real-Time Usage Track</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <!-- Benefit 1: Freeze Days -->
                    <div class="glass-card p-6 rounded-2xl border border-white/5 space-y-4 transition hover:border-[#ff5b00]/40">
                        <div class="flex items-center justify-between">
                            <span class="p-3 rounded-xl bg-blue-500/10 text-blue-400 text-2xl">
                                <i class="ri-snowflake-line"></i>
                            </span>
                            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-white/5 text-gray-300">
                                {{ $activeSub ? $activeSub->remaining_freeze_days : 0 }} Days Remaining
                            </span>
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-gray-300">Freeze Allowance</h5>
                            <p class="text-2xl font-black text-white mt-1">
                                {{ $activeSub ? $activeSub->remaining_freeze_days : 0 }} <span class="text-xs font-bold text-gray-400">/ {{ $activeSub ? ($activeSub->plan?->freeze_days ?? 0) : 0 }} Days</span>
                            </p>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                            @php
                                $totalFreeze = $activeSub ? max($activeSub->plan?->freeze_days ?? 1, 1) : 1;
                                $remFreeze = $activeSub ? $activeSub->remaining_freeze_days : 0;
                                $pctFreeze = min(100, max(0, round(($remFreeze / $totalFreeze) * 100)));
                            @endphp
                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $pctFreeze }}%"></div>
                        </div>
                    </div>

                    <!-- Benefit 2: Invitations -->
                    <div class="glass-card p-6 rounded-2xl border border-white/5 space-y-4 transition hover:border-[#ff5b00]/40">
                        <div class="flex items-center justify-between">
                            <span class="p-3 rounded-xl bg-purple-500/10 text-purple-400 text-2xl">
                                <i class="ri-user-add-line"></i>
                            </span>
                            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-white/5 text-gray-300">
                                {{ $activeSub ? $activeSub->remaining_invitations : 0 }} Passes Left
                            </span>
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-gray-300">Guest Invitations</h5>
                            <p class="text-2xl font-black text-white mt-1">
                                {{ $activeSub ? $activeSub->remaining_invitations : 0 }} <span class="text-xs font-bold text-gray-400">/ {{ $activeSub ? ($activeSub->plan?->invitations ?? 0) : 0 }} Guests</span>
                            </p>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                            @php
                                $totalInv = $activeSub ? max($activeSub->plan?->invitations ?? 1, 1) : 1;
                                $remInv = $activeSub ? $activeSub->remaining_invitations : 0;
                                $pctInv = min(100, max(0, round(($remInv / $totalInv) * 100)));
                            @endphp
                            <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $pctInv }}%"></div>
                        </div>
                    </div>

                    <!-- Benefit 3: InBody Scans -->
                    <div class="glass-card p-6 rounded-2xl border border-white/5 space-y-4 transition hover:border-[#ff5b00]/40">
                        <div class="flex items-center justify-between">
                            <span class="p-3 rounded-xl bg-green-500/10 text-green-400 text-2xl">
                                <i class="ri-scales-3-line"></i>
                            </span>
                            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-white/5 text-gray-300">
                                {{ $activeSub ? $activeSub->remaining_inbody_scans : 0 }} Scans Left
                            </span>
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-gray-300">InBody Body Composition</h5>
                            <p class="text-2xl font-black text-white mt-1">
                                {{ $activeSub ? $activeSub->remaining_inbody_scans : 0 }} <span class="text-xs font-bold text-gray-400">/ {{ $activeSub ? ($activeSub->plan?->inbody_scans ?? 0) : 0 }} Scans</span>
                            </p>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                            @php
                                $totalInBody = $activeSub ? max($activeSub->plan?->inbody_scans ?? 1, 1) : 1;
                                $remInBody = $activeSub ? $activeSub->remaining_inbody_scans : 0;
                                $pctInBody = min(100, max(0, round(($remInBody / $totalInBody) * 100)));
                            @endphp
                            <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $pctInBody }}%"></div>
                        </div>
                    </div>

                    <!-- Benefit 4: PT Sessions -->
                    <div class="glass-card p-6 rounded-2xl border border-white/5 space-y-4 transition hover:border-[#ff5b00]/40">
                        <div class="flex items-center justify-between">
                            <span class="p-3 rounded-xl bg-orange-500/10 text-[#ff5b00] text-2xl">
                                <i class="ri-user-star-line"></i>
                            </span>
                            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-white/5 text-gray-300">
                                {{ $activeSub ? $activeSub->remaining_pt_sessions : 0 }} Sessions Left
                            </span>
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-gray-300">Personal Trainer (P.T)</h5>
                            <p class="text-2xl font-black text-white mt-1">
                                {{ $activeSub ? $activeSub->remaining_pt_sessions : 0 }} <span class="text-xs font-bold text-gray-400">/ {{ $activeSub ? ($activeSub->plan?->pt_sessions ?? 0) : 0 }} Sessions</span>
                            </p>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                            @php
                                $totalPt = $activeSub ? max($activeSub->plan?->pt_sessions ?? 1, 1) : 1;
                                $remPt = $activeSub ? $activeSub->remaining_pt_sessions : 0;
                                $pctPt = min(100, max(0, round(($remPt / $totalPt) * 100)));
                            @endphp
                            <div class="bg-[#ff5b00] h-1.5 rounded-full" style="width: {{ $pctPt }}%"></div>
                        </div>
                    </div>

                    <!-- Benefit 5: Kickboxing Classes -->
                    <div class="glass-card p-6 rounded-2xl border border-white/5 space-y-4 transition hover:border-[#ff5b00]/40">
                        <div class="flex items-center justify-between">
                            <span class="p-3 rounded-xl bg-red-500/10 text-red-400 text-2xl">
                                <i class="ri-boxing-line"></i>
                            </span>
                            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-white/5 text-gray-300">
                                {{ $activeSub ? $activeSub->remaining_kickboxing_classes : 0 }} Classes Left
                            </span>
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-gray-300">Kickboxing Sessions</h5>
                            <p class="text-2xl font-black text-white mt-1">
                                {{ $activeSub ? $activeSub->remaining_kickboxing_classes : 0 }} <span class="text-xs font-bold text-gray-400">/ {{ $activeSub ? ($activeSub->plan?->kickboxing_classes ?? 0) : 0 }} Classes</span>
                            </p>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                            @php
                                $totalKb = $activeSub ? max($activeSub->plan?->kickboxing_classes ?? 1, 1) : 1;
                                $remKb = $activeSub ? $activeSub->remaining_kickboxing_classes : 0;
                                $pctKb = min(100, max(0, round(($remKb / $totalKb) * 100)));
                            @endphp
                            <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $pctKb }}%"></div>
                        </div>
                    </div>

                    <!-- Benefit 6: Custom Nutrition Plans -->
                    <div class="glass-card p-6 rounded-2xl border border-white/5 space-y-4 transition hover:border-[#ff5b00]/40">
                        <div class="flex items-center justify-between">
                            <span class="p-3 rounded-xl bg-teal-500/10 text-teal-400 text-2xl">
                                <i class="ri-restaurant-line"></i>
                            </span>
                            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-white/5 text-gray-300">
                                {{ $activeSub ? $activeSub->remaining_nutrition_plans : 0 }} Plans Left
                            </span>
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-gray-300">Nutrition & Diet Plans</h5>
                            <p class="text-2xl font-black text-white mt-1">
                                {{ $activeSub ? $activeSub->remaining_nutrition_plans : 0 }} <span class="text-xs font-bold text-gray-400">/ {{ $activeSub ? ($activeSub->plan?->nutrition_plans ?? 0) : 0 }} Diets</span>
                            </p>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                            @php
                                $totalNut = $activeSub ? max($activeSub->plan?->nutrition_plans ?? 1, 1) : 1;
                                $remNut = $activeSub ? $activeSub->remaining_nutrition_plans : 0;
                                $pctNut = min(100, max(0, round(($remNut / $totalNut) * 100)));
                            @endphp
                            <div class="bg-teal-500 h-1.5 rounded-full" style="width: {{ $pctNut }}%"></div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Upcoming Booked Trainer Sessions Table -->
            <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <h3 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                            <i class="ri-calendar-event-line neon-accent"></i> Booked Trainer Sessions
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">Your upcoming private training sessions with certified coaches</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-white/10 text-gray-400 uppercase font-black tracking-wider">
                                <th class="py-3 px-4">Trainer</th>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Time Slot</th>
                                <th class="py-3 px-4">Price</th>
                                <th class="py-3 px-4 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 font-semibold text-gray-300">
                            @forelse($upcomingBookings as $booking)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="py-4 px-4 font-bold text-white flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-neon-gradient text-white flex items-center justify-center font-black">
                                            {{ strtoupper(substr($booking->trainer?->name ?? 'T', 0, 1)) }}
                                        </div>
                                        <span>{{ $booking->trainer?->name ?? 'Private Trainer' }}</span>
                                    </td>
                                    <td class="py-4 px-4">{{ $booking->booking_date }}</td>
                                    <td class="py-4 px-4 text-[#ff5b00] font-black">
                                        {{ $booking->start_time }} - {{ $booking->end_time }}
                                    </td>
                                    <td class="py-4 px-4 font-black text-white">
                                        {{ number_format($booking->price, 2) }} EGP
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider 
                                            {{ $booking->status === 'confirmed' ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                                            {{ $booking->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400 italic">
                                        No upcoming booked trainer sessions found. 
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Agreed Membership Contract & Gym Regulations Table Section -->
            <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-white/10 pb-4">
                    <div>
                        <h3 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                            <i class="ri-file-shield-line neon-accent"></i> Agreed Gym Terms & Regulations
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">Official membership rules agreed upon at subscription checkout</p>
                    </div>

                    <span class="px-3.5 py-1.5 rounded-full bg-green-500/20 text-green-400 border border-green-500/30 text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
                        <i class="ri-checkbox-circle-fill"></i> Agreed & Accepted
                    </span>
                </div>

                <!-- 2-Column Rules Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-300">
                    @foreach($gymRules as $rule)
                        <div class="p-3.5 rounded-xl bg-white/5 border border-white/5 flex items-start gap-3 hover:border-white/10 transition">
                            <span class="px-2 py-0.5 rounded bg-neon-gradient text-white font-black text-[10px] shrink-0 mt-0.5">
                                #{{ $rule->rule_number }}
                            </span>
                            <p class="leading-relaxed">{{ $rule->rule_text }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 font-bold text-xs flex items-center gap-2">
                    <i class="ri-error-warning-line text-lg shrink-0"></i>
                    <span>Violation of any gym rules and regulations will result in immediate contract cancellation without refund.</span>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
