<x-app-layout>
    <x-slot name="title">My Workout Routine</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                <i class="ri-heart-pulse-line text-pink-400"></i> My Personalized Workout Routine
            </h2>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-widest hidden sm:inline">Custom Training Protocol</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        @if($activeRoutine)
            <!-- Active Workout Header Card -->
            <div class="glass-card p-6 sm:p-8 rounded-3xl border border-white/10 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-pink-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-pink-500/20 text-pink-400 border border-pink-500/30">
                                Active Program
                            </span>
                            @if($activeRoutine->goal)
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/5 border border-white/10 text-gray-300">
                                    <i class="ri-focus-2-line mr-1 text-[#ff5b00]"></i> Goal: {{ $activeRoutine->goal }}
                                </span>
                            @endif
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-wider">
                            {{ $activeRoutine->title }}
                        </h1>
                        @if($activeRoutine->notes)
                            <p class="text-sm text-gray-300 max-w-3xl leading-relaxed">
                                <i class="ri-information-line text-[#ff5b00] mr-1"></i> {{ $activeRoutine->notes }}
                            </p>
                        @endif
                    </div>

                    <!-- Coach Badge & Chat Action -->
                    @if($activeRoutine->trainer)
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/10 shrink-0">
                            <div class="w-12 h-12 rounded-xl bg-neon-gradient flex items-center justify-center font-black text-white text-xl shadow-lg">
                                <i class="ri-user-star-line"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Assigned Coach</span>
                                <span class="text-sm font-black text-white block">{{ $activeRoutine->trainer->name }}</span>
                                <a href="{{ route('chat.start', $activeRoutine->trainer->id) }}" class="text-xs font-bold text-[#ff5b00] hover:underline inline-flex items-center gap-1 mt-0.5">
                                    <i class="ri-chat-smile-2-line"></i> Message Coach
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Exercises Grouped by Day -->
            @php
                $groupedExercises = $activeRoutine->exercises->groupBy('day_name');
            @endphp

            @if($groupedExercises->count() > 0)
                <div class="space-y-6">
                    <h3 class="text-lg font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="ri-calendar-todo-line text-[#ff5b00]"></i> Daily Training Schedule & Exercises
                    </h3>

                    <div class="grid grid-cols-1 gap-6">
                        @foreach($groupedExercises as $dayName => $exercises)
                            <div class="glass-card rounded-2xl border border-white/10 overflow-hidden shadow-xl">
                                <div class="bg-white/5 px-6 py-4 border-b border-white/10 flex items-center justify-between">
                                    <h4 class="font-black text-base text-white uppercase tracking-wide flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-pink-400"></span>
                                        {{ $dayName ?: 'Workout Day' }}
                                    </h4>
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest bg-white/5 px-3 py-1 rounded-full">
                                        {{ $exercises->count() }} Exercises
                                    </span>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm">
                                        <thead>
                                            <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-white/5 bg-white/[0.02]">
                                                <th class="px-6 py-3.5">#</th>
                                                <th class="px-6 py-3.5">Exercise Name</th>
                                                <th class="px-6 py-3.5">Target Muscle</th>
                                                <th class="px-6 py-3.5 text-center">Sets</th>
                                                <th class="px-6 py-3.5 text-center">Reps</th>
                                                <th class="px-6 py-3.5 text-center">Rest</th>
                                                <th class="px-6 py-3.5">Coach Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/5">
                                            @foreach($exercises as $index => $ex)
                                                <tr class="hover:bg-white/[0.03] transition">
                                                    <td class="px-6 py-4 font-mono text-xs text-gray-400">{{ $index + 1 }}</td>
                                                    <td class="px-6 py-4 font-black text-white">{{ $ex->exercise_name }}</td>
                                                    <td class="px-6 py-4">
                                                        @if($ex->target_muscle)
                                                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white/5 border border-white/10 text-gray-300">
                                                                {{ $ex->target_muscle }}
                                                            </span>
                                                        @else
                                                            <span class="text-xs text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 text-center font-bold text-pink-400">{{ $ex->sets }}</td>
                                                    <td class="px-6 py-4 text-center font-mono font-bold text-white">{{ $ex->reps }}</td>
                                                    <td class="px-6 py-4 text-center font-mono text-xs text-gray-300">
                                                        {{ $ex->rest_seconds ? $ex->rest_seconds . 's' : '60s' }}
                                                    </td>
                                                    <td class="px-6 py-4 text-xs text-gray-400 italic">
                                                        {{ $ex->notes ?: '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
                    <div class="w-20 h-20 rounded-3xl bg-pink-500/10 border border-pink-500/30 text-pink-400 flex items-center justify-center text-3xl mx-auto shadow-lg shadow-pink-500/10">
                        <i class="ri-heart-pulse-line"></i>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-2xl font-black text-white uppercase tracking-wider">No Workout Routine Assigned Yet</h3>
                        <p class="text-sm text-gray-300 leading-relaxed">
                            Your certified coach is currently designing your customized workout routine according to your fitness goals. Book a private coach session or send a direct message on Live Chat!
                        </p>
                    </div>
                    <div class="pt-2 flex flex-wrap items-center justify-center gap-4">
                        <a href="{{ route('bookings.index') }}" class="px-6 py-3 rounded-full bg-neon-gradient text-white font-bold text-xs uppercase tracking-wider hover:opacity-90 transition shadow-lg shadow-[#ff5b00]/30">
                            <i class="ri-calendar-check-line mr-1.5"></i> Book Private Coach
                        </a>
                        <a href="{{ route('chat.index') }}" class="px-6 py-3 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition border border-white/10">
                            <i class="ri-chat-smile-2-line mr-1.5"></i> Open Live Chat
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
