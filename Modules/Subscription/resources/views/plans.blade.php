<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                <span class="neon-accent">Gym Membership</span> Plans & Schedules
            </h2>
            <div class="text-xs text-gray-400 font-bold uppercase tracking-widest">
                Official Egyptian Gym Offers
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">

            <!-- Success Alert -->
            @if(session('status') === 'subscribed')
                <div class="p-4 rounded-xl glass-card border border-green-500/30 text-green-400 font-bold text-sm flex items-center gap-2">
                    <i class="ri-checkbox-circle-fill text-xl"></i>
                    <span>Congratulations! You have successfully subscribed to the gym plan.</span>
                </div>
            @endif

            <!-- Section 1: Real Subscription Plans in English -->
            <div class="space-y-6">
                <div class="text-center space-y-2">
                    <span class="px-4 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-[#ff5b00]">
                        SPECIAL OFFERS
                    </span>
                    <h3 class="text-3xl font-black text-white uppercase tracking-wide">
                        Choose Your <span class="neon-accent">Fitness Package</span>
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($plans as $plan)
                        <x-subscription-plan-card :plan="$plan" :show-subscribe-form="true" />
                    @endforeach
                </div>
            </div>

            <!-- Section 2: Real Gym Operating Schedules in 100% English -->
            <div class="space-y-6 pt-6 border-t border-white/5">
                <div class="text-center space-y-2">
                    <span class="px-4 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-[#ff5b00]">
                        WORKING HOURS
                    </span>
                    <h3 class="text-3xl font-black text-white uppercase tracking-wide">
                        Gym Operating <span class="neon-accent">Schedules</span>
                    </h3>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Men's Schedule Card -->
                    <div class="glass-card rounded-2xl p-6 border border-white/5 space-y-4">
                        <div class="flex items-center justify-between pb-4 border-b border-white/10">
                            <h4 class="text-xl font-bold text-white flex items-center gap-2">
                                <i class="ri-men-line text-[#ff5b00] text-2xl"></i> Men's Operating Hours
                            </h4>
                            <span class="text-xs text-gray-400 font-bold">Men Shift Hours</span>
                        </div>

                        <div class="space-y-3">
                            @foreach($menSchedules as $sch)
                                <div class="p-3.5 rounded-xl bg-white/5 border border-white/5 flex items-center justify-between text-sm">
                                    <div class="font-bold text-gray-200">{{ $sch->days_label }}</div>
                                    <div class="text-[#ff5b00] font-black text-xs">{{ $sch->time_label }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Women's Schedule Card -->
                    <div class="glass-card rounded-2xl p-6 border border-white/5 space-y-4">
                        <div class="flex items-center justify-between pb-4 border-b border-white/10">
                            <h4 class="text-xl font-bold text-white flex items-center gap-2">
                                <i class="ri-women-line text-pink-500 text-2xl"></i> Women's Operating Hours
                            </h4>
                            <span class="text-xs text-gray-400 font-bold">Women Shift Hours</span>
                        </div>

                        <div class="space-y-3">
                            @foreach($womenSchedules as $sch)
                                <div class="p-3.5 rounded-xl bg-white/5 border border-white/5 flex items-center justify-between text-sm {{ $sch->is_off_day ? 'border-red-500/20 bg-red-500/5' : '' }}">
                                    <div class="font-bold {{ $sch->is_off_day ? 'text-red-400' : 'text-gray-200' }}">
                                        {{ $sch->days_label }}
                                    </div>
                                    <div class="font-black text-xs {{ $sch->is_off_day ? 'text-red-400' : 'text-[#ff5b00]' }}">
                                        {{ $sch->time_label }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
