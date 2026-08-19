<x-app-layout>
    <x-slot name="title">My Nutrition & Diet Plan</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                <i class="ri-restaurant-2-line text-emerald-400"></i> My Nutrition & Diet Protocol
            </h2>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-widest hidden sm:inline">Macro Targets & Meal Timings</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        @if($activeDiet)
            <!-- Active Diet Overview Card -->
            <div class="glass-card p-6 sm:p-8 rounded-3xl border border-white/10 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                Active Meal Plan
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-wider">
                            {{ $activeDiet->title }}
                        </h1>
                        @if($activeDiet->notes)
                            <p class="text-sm text-gray-300 max-w-3xl leading-relaxed">
                                <i class="ri-information-line text-[#ff5b00] mr-1"></i> {{ $activeDiet->notes }}
                            </p>
                        @endif
                    </div>

                    <!-- Coach Badge & Chat Action -->
                    @if($activeDiet->trainer)
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/10 shrink-0">
                            <div class="w-12 h-12 rounded-xl bg-neon-gradient flex items-center justify-center font-black text-white text-xl shadow-lg">
                                <i class="ri-user-star-line"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Prescribing Coach</span>
                                <span class="text-sm font-black text-white block">{{ $activeDiet->trainer->name }}</span>
                                <a href="{{ route('chat.start', $activeDiet->trainer->id) }}" class="text-xs font-bold text-[#ff5b00] hover:underline inline-flex items-center gap-1 mt-0.5">
                                    <i class="ri-chat-smile-2-line"></i> Message Coach
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Daily Macro Target Badges Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-6 border-t border-white/10">
                    <!-- Daily Calories -->
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1">
                            <i class="ri-fire-line text-[#ff5b00]"></i> Daily Calories
                        </span>
                        <p class="text-xl font-black text-white font-mono">{{ $activeDiet->daily_calories }} <span class="text-xs font-normal text-gray-400">kcal</span></p>
                    </div>

                    <!-- Protein -->
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1">
                            <i class="ri-heart-pulse-line text-blue-400"></i> Protein
                        </span>
                        <p class="text-xl font-black text-blue-400 font-mono">{{ $activeDiet->protein_grams }} <span class="text-xs font-normal text-gray-400">g</span></p>
                    </div>

                    <!-- Carbs -->
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1">
                            <i class="ri-bread-line text-amber-400"></i> Carbohydrates
                        </span>
                        <p class="text-xl font-black text-amber-400 font-mono">{{ $activeDiet->carbs_grams }} <span class="text-xs font-normal text-gray-400">g</span></p>
                    </div>

                    <!-- Fats -->
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1">
                            <i class="ri-drop-line text-emerald-400"></i> Healthy Fats
                        </span>
                        <p class="text-xl font-black text-emerald-400 font-mono">{{ $activeDiet->fats_grams }} <span class="text-xs font-normal text-gray-400">g</span></p>
                    </div>
                </div>
            </div>

            <!-- Daily Meals Timeline & Menu -->
            @if($activeDiet->meals->count() > 0)
                <div class="space-y-6">
                    <h3 class="text-lg font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="ri-restaurant-line text-emerald-400"></i> Prescribed Meals & Timings
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($activeDiet->meals as $meal)
                            <div class="glass-card p-6 rounded-2xl border border-white/10 space-y-4 hover:border-emerald-500/30 transition shadow-xl">
                                <div class="flex items-center justify-between pb-3 border-b border-white/10">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-black text-lg border border-emerald-500/20">
                                            <i class="ri-cup-line"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-black text-base text-white uppercase tracking-wide">{{ $meal->meal_name }}</h4>
                                            <span class="text-xs font-bold text-[#ff5b00] flex items-center gap-1 font-mono">
                                                <i class="ri-time-line"></i> {{ $meal->meal_time ?: 'As scheduled' }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($meal->calories)
                                        <span class="px-3 py-1 rounded-full bg-white/5 text-gray-300 text-xs font-mono font-bold">
                                            {{ $meal->calories }} kcal
                                        </span>
                                    @endif
                                </div>

                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Food Items & Ingredients:</span>
                                    <p class="text-sm text-gray-200 leading-relaxed whitespace-pre-line bg-white/[0.02] p-3 rounded-xl border border-white/5">
                                        {{ $meal->food_items }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <!-- Centered Empty State with Ample Margin & Solid Glass Card -->
            <div class="min-h-[50vh] flex items-center justify-center my-8">
                <div class="bg-[#12141c]/95 backdrop-blur-2xl p-8 sm:p-12 rounded-3xl border border-white/10 text-center space-y-6 max-w-xl mx-auto shadow-2xl shadow-black/80">
                    <div class="w-20 h-20 rounded-3xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-3xl mx-auto shadow-lg shadow-emerald-500/10">
                        <i class="ri-restaurant-2-line"></i>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-2xl font-black text-white uppercase tracking-wider">No Nutrition Plan Assigned Yet</h3>
                        <p class="text-sm text-gray-300 leading-relaxed">
                            Your personal coach will calculate your daily caloric intake and macro split (Protein, Carbs, Fats) and assign a structured meal schedule.
                        </p>
                    </div>
                    <div class="pt-2 flex flex-wrap items-center justify-center gap-4">
                        <a href="{{ route('bookings.index') }}" class="px-6 py-3 rounded-full bg-neon-gradient text-white font-bold text-xs uppercase tracking-wider hover:opacity-90 transition shadow-lg shadow-[#ff5b00]/30">
                            <i class="ri-calendar-check-line mr-1.5"></i> Book Private Coach
                        </a>
                        <a href="{{ route('chat.index') }}" class="px-6 py-3 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition border border-white/10">
                            <i class="ri-chat-smile-2-line mr-1.5"></i> Message Coach on Chat
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>