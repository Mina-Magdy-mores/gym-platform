<x-app-layout>
    <x-slot name="title">Assign Workout Routine</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                <i class="ri-dumbbell-line text-[#ff5b00]"></i> Assign Workout Routine for {{ $user->name }}
            </h2>
            <a href="{{ route('trainer.members.index') }}" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-black uppercase transition">
                <i class="ri-arrow-left-line"></i> Back to Roster
            </a>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        title: @js(old('title', $existingRoutine?->title ?? '')),
        goal: @js(old('goal', $existingRoutine?->goal ?? '')),
        status: @js(old('status', $existingRoutine?->status ?? 'active')),
        notes: @js(old('notes', $existingRoutine?->notes ?? '')),
        availableTemplates: @js($availableRoutines->toArray()),
        selectedTemplateId: '',
        exercises: @js(old('exercises', $existingRoutine && $existingRoutine->exercises->count() > 0 ? $existingRoutine->exercises->toArray() : [
            ['day_name' => 'Day 1 - Chest & Triceps', 'exercise_name' => 'Barbell Bench Press', 'target_muscle' => 'Chest', 'sets' => 4, 'reps' => '8-12', 'rest_seconds' => 90, 'notes' => ''],
            ['day_name' => 'Day 1 - Chest & Triceps', 'exercise_name' => 'Incline Dumbbell Press', 'target_muscle' => 'Upper Chest', 'sets' => 3, 'reps' => '10-12', 'rest_seconds' => 60, 'notes' => '']
        ])),
        loadTemplate(templateId) {
            if (!templateId) return;
            let found = this.availableTemplates.find(t => t.id == templateId);
            if (found) {
                this.title = found.title;
                this.goal = found.goal || '';
                this.notes = found.notes || '';
                this.status = 'active';
                if (found.exercises && found.exercises.length > 0) {
                    this.exercises = found.exercises.map(ex => ({
                        day_name: ex.day_name || 'Day 1',
                        exercise_name: ex.exercise_name || '',
                        target_muscle: ex.target_muscle || '',
                        sets: ex.sets || 3,
                        reps: ex.reps || '8-12',
                        rest_seconds: ex.rest_seconds || 60,
                        notes: ex.notes || ''
                    }));
                }
            }
        },
        addExercise() {
            this.exercises.push({ day_name: 'Day 1', exercise_name: '', target_muscle: '', sets: 3, reps: '10-12', rest_seconds: 60, notes: '' });
        },
        removeExercise(index) {
            if (this.exercises.length > 1) {
                this.exercises.splice(index, 1);
            }
        }
    }">
        <form action="{{ route('trainer.workout.store', $user->id) }}" method="POST" class="space-y-6">
            @csrf

            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs font-bold space-y-1">
                    <div class="flex items-center gap-2 text-sm font-black uppercase">
                        <i class="ri-error-warning-line text-lg"></i> Validation Error Occurred
                    </div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Preset Template Selector Card -->
            <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-5 shadow-2xl space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="ri-folder-open-line text-[#ff5b00]"></i> Load System Template or Past Routine
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Select any previously created routine to instantly auto-fill all its exercises and details</p>
                    </div>
                </div>

                <div class="relative">
                    <select x-model="selectedTemplateId" @change="loadTemplate($event.target.value)" class="w-full bg-[#1a1d28] border border-white/15 rounded-xl text-white text-xs p-3 focus:border-[#ff5b00]">
                        <option value="">-- Choose Past Routine / Master Template to Auto-Fill --</option>
                        @foreach($availableRoutines as $routineOption)
                            <option value="{{ $routineOption->id }}">
                                📋 {{ $routineOption->title }} ({{ $routineOption->exercises->count() }} Exercises) - Status: {{ strtoupper($routineOption->status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Program Header Card -->
            <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="ri-file-list-3-line text-[#ff5b00]"></i> Routine Overview
                    </h3>
                    @if($existingRoutine)
                        <span class="px-2.5 py-1 rounded-lg bg-orange-500/10 border border-orange-500/30 text-orange-400 text-xs font-black uppercase">
                            Editing Existing Active Routine #{{ $existingRoutine->id }}
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Routine Title *</label>
                        <input type="text" name="title" x-model="title" required placeholder="e.g. 4-Week Hypertrophy Muscle Split" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Primary Goal</label>
                        <input type="text" name="goal" x-model="goal" placeholder="e.g. Muscle Gain / Strength Building" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Status (Visibility)</label>
                        <select name="status" x-model="status" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]">
                            <option value="active">Active (Visible on Member Dashboard)</option>
                            <option value="archived">Archived (Hide from Member Dashboard)</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Coach General Notes</label>
                    <textarea name="notes" x-model="notes" rows="2" placeholder="e.g. Warm up 5-10 mins before starting heavy sets" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]"></textarea>
                </div>
            </div>

            <!-- Dynamic Exercises Builder Card -->
            <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-6">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="ri-dumbbell-line text-[#ff5b00]"></i> Exercises Schedule
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Build routine exercises dynamically</p>
                    </div>
                    <button type="button" @click="addExercise()" class="px-4 py-2 bg-[#ff5b00] hover:bg-[#ff5b00]/90 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg transition flex items-center gap-1.5 cursor-pointer">
                        <i class="ri-add-line text-base"></i> Add Exercise
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(ex, index) in exercises" :key="index">
                        <div class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-3 relative">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black text-[#ff5b00] uppercase font-mono" x-text="'Exercise #' + (index + 1)"></span>
                                <button type="button" @click="removeExercise(index)" x-show="exercises.length > 1" class="text-red-400 hover:text-red-300 transition text-xs font-bold flex items-center gap-1 cursor-pointer">
                                    <i class="ri-delete-bin-line"></i> Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-3">
                                <div class="lg:col-span-2 space-y-1">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase">Day / Split</label>
                                    <input type="text" :name="'exercises[' + index + '][day_name]'" x-model="ex.day_name" required placeholder="Day 1 - Chest" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-2.5">
                                </div>

                                <div class="lg:col-span-2 space-y-1">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase">Exercise Name *</label>
                                    <input type="text" :name="'exercises[' + index + '][exercise_name]'" x-model="ex.exercise_name" required placeholder="e.g. Bench Press" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-2.5">
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase">Sets</label>
                                    <input type="number" :name="'exercises[' + index + '][sets]'" x-model="ex.sets" required min="1" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-2.5">
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase">Reps</label>
                                    <input type="text" :name="'exercises[' + index + '][reps]'" x-model="ex.reps" required placeholder="8-12" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-2.5">
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase">Rest (Sec)</label>
                                    <input type="number" :name="'exercises[' + index + '][rest_seconds]'" x-model="ex.rest_seconds" placeholder="60" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-2.5">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="pt-4 border-t border-white/10 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-neon-gradient text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg hover:opacity-90 transition cursor-pointer">
                        Assign Program to Athlete
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
