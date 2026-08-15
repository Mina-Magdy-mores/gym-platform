<x-app-layout>
    <x-slot name="title">{{ Auth::user()->hasRole('admin') ? 'Coaches Workouts & Diets Oversight' : 'Trainer Assigned Members' }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                    <i class="ri-user-heart-line text-[#ff5b00]"></i>
                    {{ Auth::user()->hasRole('admin') ? 'Coaches Workouts & Diets Oversight' : 'Assigned Athletes Roster' }}
                </h2>
                <p class="text-xs text-gray-400 mt-1">
                    {{ Auth::user()->hasRole('admin') ? 'Monitor coaching performance, review athlete assignments, and inspect customized workout & diet plans' : 'Assign custom workout routines and nutrition diet plans for your members' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <div class="px-3 py-1.5 bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 rounded-xl text-xs font-black uppercase">
                    {{ Auth::user()->hasRole('admin') ? 'Coaches: ' . $trainers->count() : 'Active Athletes: ' . $members->count() }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8" x-data="{
        activeTab: '{{ Auth::user()->hasRole('admin') ? 'coaches' : 'athletes' }}',
        selectedWorkout: null,
        selectedDiet: null,
        showWorkoutModal: false,
        showDietModal: false,

        inspectWorkout(workout) {
            this.selectedWorkout = workout;
            this.showWorkoutModal = true;
        },

        inspectDiet(diet) {
            this.selectedDiet = diet;
            this.showDietModal = true;
        }
    }">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-green-500/20 border border-green-500/30 text-green-400 font-bold text-sm flex items-center gap-2">
                <i class="ri-checkbox-circle-fill text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(Auth::user()->hasRole('admin'))
            <!-- Admin Navigation Tabs -->
            <div class="flex items-center gap-3 border-b border-white/10 pb-3">
                <button
                    @click="activeTab = 'coaches'"
                    type="button"
                    :class="activeTab === 'coaches' ? 'bg-[#ff5b00] text-white shadow-lg' : 'bg-white/5 text-gray-400 hover:text-white hover:bg-white/10'"
                    class="px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition cursor-pointer flex items-center gap-2"
                >
                    <i class="ri-user-star-line text-sm"></i> Coaches Performance & Plans ({{ $trainers->count() }})
                </button>
                <button
                    @click="activeTab = 'athletes'"
                    type="button"
                    :class="activeTab === 'athletes' ? 'bg-[#ff5b00] text-white shadow-lg' : 'bg-white/5 text-gray-400 hover:text-white hover:bg-white/10'"
                    class="px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition cursor-pointer flex items-center gap-2"
                >
                    <i class="ri-user-heart-line text-sm"></i> Platform Athletes Roster ({{ $members->count() }})
                </button>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- TAB 1: COACHES OVERSIGHT (ADMIN ONLY)      -->
        <!-- ========================================== -->
        @if(Auth::user()->hasRole('admin'))
            <div x-show="activeTab === 'coaches'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($trainers as $trainer)
                        @php
                            $cleanCoachName = preg_replace('/^(Captain|Coach)\s+/i', '', $trainer->name);
                            $workoutCount = $trainer->createdWorkoutRoutines->count();
                            $dietCount = $trainer->createdDietPlans->count();
                        @endphp
                        <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-6 hover:border-[#ff5b00]/40 transition duration-200">
                            <!-- Coach Info Header -->
                            <div class="flex items-center justify-between gap-4 pb-4 border-b border-white/10">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    @if($trainer->hasMedia('avatar'))
                                        <img src="{{ $trainer->getFirstMediaUrl('avatar', 'thumb') }}" alt="{{ $trainer->name }}" class="w-12 h-12 rounded-2xl object-cover border border-[#ff5b00] shrink-0">
                                    @else
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-sky-600 to-indigo-700 flex items-center justify-center font-black text-white text-lg shadow-lg shrink-0 uppercase">
                                            {{ mb_substr($cleanCoachName, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <h3 class="font-black text-white text-base tracking-wide truncate">{{ $trainer->name }}</h3>
                                        <p class="text-gray-400 text-xs truncate">{{ $trainer->email }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('chat.show', $trainer->id) }}" class="px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/15 text-gray-200 hover:text-white text-xs font-bold transition flex items-center gap-1 border border-white/10">
                                        <i class="ri-chat-smile-2-line text-[#ff5b00]"></i> Chat
                                    </a>
                                </div>
                            </div>

                            <!-- Workout Routines Section -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-black text-white uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="ri-dumbbell-line text-[#ff5b00]"></i> Created Workout Routines ({{ $workoutCount }})
                                    </span>
                                </div>

                                @if($trainer->createdWorkoutRoutines->isNotEmpty())
                                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                        @foreach($trainer->createdWorkoutRoutines as $wrk)
                                            <div class="p-3 rounded-xl bg-white/[0.03] border border-white/5 flex items-center justify-between gap-3 hover:border-white/15 transition text-xs">
                                                <div class="min-w-0 flex-1">
                                                    <div class="font-bold text-white truncate">{{ $wrk->title }}</div>
                                                    <div class="text-[10px] text-gray-400 flex items-center gap-2">
                                                        <span>Athlete: <strong class="text-gray-200">{{ $wrk->user->name ?? 'Unassigned' }}</strong></span>
                                                        <span>&bull;</span>
                                                        <span>{{ $wrk->exercises->count() }} Exercises</span>
                                                    </div>
                                                </div>
                                                <button
                                                    @click="inspectWorkout({{ json_encode($wrk) }})"
                                                    type="button"
                                                    class="px-2.5 py-1 rounded-lg bg-[#ff5b00]/10 hover:bg-[#ff5b00] text-[#ff5b00] hover:text-white text-[10px] font-black uppercase transition shrink-0 cursor-pointer"
                                                >
                                                    Inspect
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-3 rounded-xl bg-white/[0.02] border border-dashed border-white/10 text-center text-gray-500 text-[11px]">
                                        No workout routines crafted yet.
                                    </div>
                                @endif
                            </div>

                            <!-- Diet Plans Section -->
                            <div class="space-y-3 pt-2 border-t border-white/5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-black text-white uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="ri-restaurant-line text-green-400"></i> Created Nutrition Diet Plans ({{ $dietCount }})
                                    </span>
                                </div>

                                @if($trainer->createdDietPlans->isNotEmpty())
                                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                        @foreach($trainer->createdDietPlans as $diet)
                                            <div class="p-3 rounded-xl bg-white/[0.03] border border-white/5 flex items-center justify-between gap-3 hover:border-white/15 transition text-xs">
                                                <div class="min-w-0 flex-1">
                                                    <div class="font-bold text-white truncate">{{ $diet->title }}</div>
                                                    <div class="text-[10px] text-gray-400 flex items-center gap-2">
                                                        <span>Athlete: <strong class="text-gray-200">{{ $diet->user->name ?? 'Unassigned' }}</strong></span>
                                                        <span>&bull;</span>
                                                        <span class="text-green-400 font-mono font-bold">{{ $diet->daily_calories }} Kcal</span>
                                                    </div>
                                                </div>
                                                <button
                                                    @click="inspectDiet({{ json_encode($diet) }})"
                                                    type="button"
                                                    class="px-2.5 py-1 rounded-lg bg-green-500/10 hover:bg-green-500 text-green-400 hover:text-white text-[10px] font-black uppercase transition shrink-0 cursor-pointer"
                                                >
                                                    Inspect
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-3 rounded-xl bg-white/[0.02] border border-dashed border-white/10 text-center text-gray-500 text-[11px]">
                                        No diet nutrition plans crafted yet.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-gray-400 space-y-2">
                            <i class="ri-user-heart-line text-4xl text-gray-500"></i>
                            <p class="text-sm font-bold">No certified trainers registered</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- TAB 2: ATHLETES ROSTER                     -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'athletes'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($members as $m)
                @php
                    $cleanName = preg_replace('/[^\p{L}\p{N}\s]/u', '', $m->name);
                    $initial = strtoupper(mb_substr($cleanName, 0, 1)) ?: 'A';
                @endphp
                <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-5 hover:border-[#ff5b00]/40 transition duration-200 flex flex-col justify-between">
                    <div class="space-y-5">
                        <!-- Member Header -->
                        <div class="flex items-center gap-4 pb-4 border-b border-white/10">
                            @if($m->hasMedia('avatar'))
                                <img src="{{ $m->getFirstMediaUrl('avatar', 'thumb') }}" alt="{{ $m->name }}" class="w-14 h-14 rounded-2xl object-cover border-2 border-[#ff5b00] shadow-lg shrink-0">
                            @else
                                <div class="w-14 h-14 rounded-2xl bg-neon-gradient flex items-center justify-center font-black text-white text-xl shadow-lg shrink-0">
                                    {{ $initial }}
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
                                <div class="flex items-center gap-2 min-w-0">
                                    <i class="ri-fitness-fill text-base text-[#ff5b00] shrink-0"></i>
                                    <div class="min-w-0">
                                        <div class="text-[10px] text-gray-400 uppercase font-black">Active Routine</div>
                                        <div class="text-white font-bold text-xs truncate max-w-[150px]">
                                            {{ $m->activeWorkoutRoutine->title ?? 'None Assigned' }}
                                        </div>
                                    </div>
                                </div>
                                @if($m->activeWorkoutRoutine)
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-green-500/20 text-green-400 border border-green-500/30 shrink-0">
                                        {{ $m->activeWorkoutRoutine->exercises->count() }} Exs
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-gray-500/20 text-gray-400 border border-gray-500/30 shrink-0">
                                        Pending
                                    </span>
                                @endif
                            </div>

                            <!-- Active Diet Plan Badge -->
                            <div class="p-3 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0">
                                    <i class="ri-restaurant-fill text-base text-green-400 shrink-0"></i>
                                    <div class="min-w-0">
                                        <div class="text-[10px] text-gray-400 uppercase font-black">Active Diet Plan</div>
                                        <div class="text-white font-bold text-xs truncate max-w-[150px]">
                                            {{ $m->activeDietPlan->title ?? 'None Assigned' }}
                                        </div>
                                    </div>
                                </div>
                                @if($m->activeDietPlan)
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-green-500/20 text-green-400 border border-green-500/30 shrink-0">
                                        {{ $m->activeDietPlan->daily_calories }} Kcal
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-gray-500/20 text-gray-400 border border-gray-500/30 shrink-0">
                                        Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2 pt-4 border-t border-white/10 mt-4">
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('trainer.workout.create', $m->id) }}" class="py-2.5 px-3 rounded-xl bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 hover:bg-[#ff5b00] hover:text-white transition font-black text-[10px] uppercase tracking-wider flex items-center justify-center gap-1.5 text-center">
                                <i class="ri-dumbbell-line text-sm"></i> Assign Workout
                            </a>
                            <a href="{{ route('trainer.diet.create', $m->id) }}" class="py-2.5 px-3 rounded-xl bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-green-500 hover:text-white transition font-black text-[10px] uppercase tracking-wider flex items-center justify-center gap-1.5 text-center">
                                <i class="ri-restaurant-line text-sm"></i> Assign Diet
                            </a>
                        </div>

                        <!-- Direct Live Chat Action Button -->
                        <a href="{{ route('chat.start', $m->id) }}" class="w-full py-2.5 px-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition group">
                            <i class="ri-chat-smile-2-line text-[#ff5b00]"></i> Message Athlete Live
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-[#12141c]/90 rounded-2xl border border-white/10 text-gray-400 space-y-3">
                    <i class="ri-user-search-line text-4xl text-[#ff5b00]"></i>
                    <p class="font-bold text-sm">No members registered in the gym roster yet.</p>
                </div>
            @endforelse
        </div>

        <!-- Workout Inspection Modal -->
        <div
            x-show="showWorkoutModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;"
        >
            <div
                @click.away="showWorkoutModal = false"
                class="bg-[#12141c] border border-white/20 rounded-2xl w-full max-w-xl p-6 shadow-2xl space-y-5 max-h-[85vh] overflow-y-auto"
            >
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="ri-dumbbell-line text-[#ff5b00]"></i>
                            <span x-text="selectedWorkout ? selectedWorkout.title : 'Workout Routine'"></span>
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5" x-text="selectedWorkout ? 'Goal: ' + (selectedWorkout.goal || 'General Fitness') : ''"></p>
                    </div>
                    <button @click="showWorkoutModal = false" type="button" class="text-gray-400 hover:text-white text-xl cursor-pointer">
                        <i class="ri-close-line"></i>
                    </button>
                </div>

                <template x-if="selectedWorkout && selectedWorkout.exercises">
                    <div class="space-y-3">
                        <h4 class="text-xs font-black text-gray-300 uppercase tracking-wider">Exercise List</h4>
                        <div class="space-y-2">
                            <template x-for="(ex, idx) in selectedWorkout.exercises" :key="idx">
                                <div class="p-3 rounded-xl bg-white/[0.03] border border-white/5 flex items-center justify-between gap-3 text-xs">
                                    <div>
                                        <span class="text-[10px] font-mono text-[#ff5b00] uppercase font-bold" x-text="ex.day_name"></span>
                                        <div class="font-bold text-white text-sm" x-text="ex.exercise_name"></div>
                                        <div class="text-gray-400 text-[11px]" x-text="ex.target_muscle ? 'Target: ' + ex.target_muscle : ''"></div>
                                    </div>
                                    <div class="text-right font-mono">
                                        <div class="text-white font-bold" x-text="ex.sets + ' Sets x ' + ex.reps"></div>
                                        <div class="text-gray-500 text-[10px]" x-text="ex.rest_seconds ? ex.rest_seconds + 's Rest' : ''"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Diet Inspection Modal -->
        <div
            x-show="showDietModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;"
        >
            <div
                @click.away="showDietModal = false"
                class="bg-[#12141c] border border-white/20 rounded-2xl w-full max-w-xl p-6 shadow-2xl space-y-5 max-h-[85vh] overflow-y-auto"
            >
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="ri-restaurant-line text-green-400"></i>
                            <span x-text="selectedDiet ? selectedDiet.title : 'Diet Plan'"></span>
                        </h3>
                        <p class="text-xs text-green-400 font-mono mt-0.5" x-text="selectedDiet ? selectedDiet.daily_calories + ' Kcal / day' : ''"></p>
                    </div>
                    <button @click="showDietModal = false" type="button" class="text-gray-400 hover:text-white text-xl cursor-pointer">
                        <i class="ri-close-line"></i>
                    </button>
                </div>

                <template x-if="selectedDiet">
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-2 text-center font-mono">
                            <div class="p-2.5 rounded-xl bg-white/[0.03] border border-white/5">
                                <span class="text-[9px] text-gray-400 uppercase block font-sans">Protein</span>
                                <span class="text-white font-black text-sm" x-text="(selectedDiet.protein_grams || 0) + 'g'"></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-white/[0.03] border border-white/5">
                                <span class="text-[9px] text-gray-400 uppercase block font-sans">Carbs</span>
                                <span class="text-white font-black text-sm" x-text="(selectedDiet.carbs_grams || 0) + 'g'"></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-white/[0.03] border border-white/5">
                                <span class="text-[9px] text-gray-400 uppercase block font-sans">Fats</span>
                                <span class="text-white font-black text-sm" x-text="(selectedDiet.fats_grams || 0) + 'g'"></span>
                            </div>
                        </div>

                        <template x-if="selectedDiet.meals && selectedDiet.meals.length > 0">
                            <div class="space-y-2">
                                <h4 class="text-xs font-black text-gray-300 uppercase tracking-wider">Scheduled Meals</h4>
                                <div class="space-y-2">
                                    <template x-for="(meal, idx) in selectedDiet.meals" :key="idx">
                                        <div class="p-3 rounded-xl bg-white/[0.03] border border-white/5 flex items-center justify-between gap-3 text-xs">
                                            <div>
                                                <span class="text-[10px] font-mono text-green-400 uppercase font-bold" x-text="meal.meal_time || 'Meal ' + (idx + 1)"></span>
                                                <div class="font-bold text-white text-sm" x-text="meal.meal_name"></div>
                                                <div class="text-gray-400 text-[11px]" x-text="meal.items_summary || ''"></div>
                                            </div>
                                            <div class="text-right font-mono">
                                                <div class="text-green-400 font-bold" x-text="meal.calories ? meal.calories + ' Kcal' : ''"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-app-layout>
