<x-app-layout>
    <x-slot name="title">Trainer Assigned Members</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                    <i class="ri-user-heart-line text-[#ff5b00]"></i> Assigned Athletes Roster
                </h2>
                <p class="text-xs text-gray-400 mt-1">Assign custom workout routines and nutrition diet plans for your members</p>
            </div>
            <div class="px-3 py-1.5 bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 rounded-xl text-xs font-black uppercase">
                Active Athletes: {{ $members->count() }}
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-green-500/20 border border-green-500/30 text-green-400 font-bold text-sm flex items-center gap-2">
                <i class="ri-checkbox-circle-fill text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($members as $m)
                <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-5 hover:border-[#ff5b00]/40 transition duration-200">
                    <!-- Member Header -->
                    <div class="flex items-center gap-4 pb-4 border-b border-white/10">
                        @if($m->getFirstMediaUrl('avatar', 'thumb'))
                            <img src="{{ $m->getFirstMediaUrl('avatar', 'thumb') }}" alt="Avatar" class="w-14 h-14 rounded-2xl object-cover border-2 border-[#ff5b00] shadow-lg shrink-0">
                        @else
                            <div class="w-14 h-14 rounded-2xl bg-neon-gradient flex items-center justify-center font-black text-white text-xl shadow-lg shrink-0">
                                {{ strtoupper(substr($m->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <h3 class="font-black text-white text-base tracking-wide truncate">{{ $m->name }}</h3>
                            <p class="text-gray-400 text-xs truncate">{{ $m->email }}</p>
                            <div class="mt-1 inline-flex items-center gap-1 text-[10px] font-mono text-[#ff5b00] font-bold uppercase">
                                <i class="ri-vip-crown-2-line"></i> {{ $m->activeSubscription->plan->name ?? 'No Plan Active' }}
                            </div>
                        </div>
                    </div>

                    <!-- Current Routines & Diet Badges -->
                    <div class="space-y-2.5 text-xs font-semibold">
                        <!-- Active Workout Routine Badge -->
                        <div class="p-3 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="ri-fitness-fill text-base text-[#ff5b00]"></i>
                                <div>
                                    <div class="text-[10px] text-gray-400 uppercase font-black">Active Routine</div>
                                    <div class="text-white font-bold text-xs truncate max-w-[150px]">
                                        {{ $m->activeWorkoutRoutine->title ?? 'None Assigned' }}
                                    </div>
                                </div>
                            </div>
                            @if($m->activeWorkoutRoutine)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-green-500/20 text-green-400 border border-green-500/30">
                                    {{ $m->activeWorkoutRoutine->exercises->count() }} Exs
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-gray-500/20 text-gray-400 border border-gray-500/30">
                                    Pending
                                </span>
                            @endif
                        </div>

                        <!-- Active Diet Plan Badge -->
                        <div class="p-3 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="ri-restaurant-fill text-base text-green-400"></i>
                                <div>
                                    <div class="text-[10px] text-gray-400 uppercase font-black">Active Diet Plan</div>
                                    <div class="text-white font-bold text-xs truncate max-w-[150px]">
                                        {{ $m->activeDietPlan->title ?? 'None Assigned' }}
                                    </div>
                                </div>
                            </div>
                            @if($m->activeDietPlan)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-green-500/20 text-green-400 border border-green-500/30">
                                    {{ $m->activeDietPlan->daily_calories }} Kcal
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-gray-500/20 text-gray-400 border border-gray-500/30">
                                    Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-white/10">
                        <a href="{{ route('trainer.workout.create', $m->id) }}" class="py-2.5 px-3 rounded-xl bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 hover:bg-[#ff5b00] hover:text-white transition font-black text-[10px] uppercase tracking-wider flex items-center justify-center gap-1.5 text-center">
                            <i class="ri-[#ff5b00] ri-dumbbell-line text-sm"></i> Assign Workout
                        </a>
                        <a href="{{ route('trainer.diet.create', $m->id) }}" class="py-2.5 px-3 rounded-xl bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-green-500 hover:text-white transition font-black text-[10px] uppercase tracking-wider flex items-center justify-center gap-1.5 text-center">
                            <i class="ri-restaurant-2-line text-sm"></i> Assign Diet Plan
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-[#12141c]/90 rounded-2xl border border-white/10 text-gray-400 space-y-3">
                    <i class="ri-user-search-line text-4xl text-[#ff5b00]"></i>
                    <p class="font-bold text-sm">No members assigned to your personal training roster yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
