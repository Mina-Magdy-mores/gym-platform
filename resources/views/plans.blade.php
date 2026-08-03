<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                <span class="neon-accent">Membership</span> Plans & Gym Schedules
            </h2>
            <span class="text-xs text-gray-400 font-bold uppercase tracking-widest hidden sm:inline">
                Choose your ideal training package
            </span>
        </div>
    </x-slot>

    <div class="py-8 space-y-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">

            <!-- Flash Status Alert -->
            @if (session('status'))
                <div class="p-4 rounded-2xl glass-card border border-green-500/40 bg-green-500/10 text-green-400 font-bold text-sm flex items-center justify-between shadow-xl">
                    <div class="flex items-center gap-3">
                        <i class="ri-checkbox-circle-fill text-xl text-green-400"></i>
                        <span>
                            @if(session('status') === 'subscription-activated')
                                Subscription activated successfully! Welcome to FIT CLUB Family!
                            @else
                                {{ session('status') }}
                            @endif
                        </span>
                    </div>
                </div>
            @endif

            <!-- 1. Subscription Plans Section -->
            <div class="space-y-6">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <h3 class="text-2xl font-black text-white uppercase tracking-wider">Official Membership Plans</h3>
                        <p class="text-xs text-gray-400 mt-1">Select a plan to activate your gym access & benefits</p>
                    </div>
                    <span class="text-xs font-black text-[#ff5b00] uppercase tracking-widest bg-[#ff5b00]/10 px-3 py-1.5 rounded-full border border-[#ff5b00]/20">
                        {{ $plans->count() }} Packages Available
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($plans as $plan)
                        <x-subscription-plan-card :plan="$plan" />
                    @endforeach
                </div>
            </div>

            <!-- 2. Gym Operating Schedules Section -->
            <div class="space-y-6 pt-6 border-t border-white/10">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/10 pb-4">
                    <div>
                        <h3 class="text-2xl font-black text-white uppercase tracking-wider">Gym Operating Hours & Shifts</h3>
                        <p class="text-xs text-gray-400 mt-1">Check working hours for Men and Ladies shifts</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Men Schedules Card -->
                    <div class="glass-card rounded-2xl p-6 border border-white/5 space-y-4">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <h4 class="text-lg font-black text-white uppercase flex items-center gap-2">
                                <i class="ri-men-line text-blue-400 text-xl"></i> Men Shifts Schedule
                            </h4>
                            <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-400 text-xs font-black uppercase tracking-wider">
                                Men Only
                            </span>
                        </div>
                        <ul class="space-y-3 text-xs">
                            @forelse($menSchedules as $sch)
                                <li class="p-3 rounded-xl bg-white/5 flex items-center justify-between hover:bg-white/10 transition">
                                    <div>
                                        <div class="font-bold text-white text-sm">{{ $sch->days_label }}</div>
                                        <div class="text-gray-400 text-[11px] mt-0.5">{{ $sch->notes ?? 'Standard Men Shift' }}</div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-black text-[#ff5b00] text-sm block">{{ $sch->time_label }}</span>
                                    </div>
                                </li>
                            @empty
                                <li class="text-gray-400 text-xs italic">No schedule posted for Men.</li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Women Schedules Card -->
                    <div class="glass-card rounded-2xl p-6 border border-white/5 space-y-4">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <h4 class="text-lg font-black text-white uppercase flex items-center gap-2">
                                <i class="ri-women-line text-pink-400 text-xl"></i> Ladies Shifts Schedule
                            </h4>
                            <span class="px-3 py-1 rounded-full bg-pink-500/20 text-pink-400 text-xs font-black uppercase tracking-wider">
                                Ladies Only
                            </span>
                        </div>
                        <ul class="space-y-3 text-xs">
                            @forelse($womenSchedules as $sch)
                                <li class="p-3 rounded-xl bg-white/5 flex items-center justify-between hover:bg-white/10 transition">
                                    <div>
                                        <div class="font-bold text-white text-sm">{{ $sch->days_label }}</div>
                                        <div class="text-gray-400 text-[11px] mt-0.5">{{ $sch->notes ?? 'Standard Ladies Shift' }}</div>
                                    </div>
                                    <div class="text-right">
                                        @if($sch->is_off_day)
                                            <span class="px-2.5 py-1 rounded-full bg-red-500/20 text-red-400 font-black text-[11px] uppercase">
                                                Day Off
                                            </span>
                                        @else
                                            <span class="font-black text-[#ff5b00] text-sm block">{{ $sch->time_label }}</span>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="text-gray-400 text-xs italic">No schedule posted for Ladies.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
