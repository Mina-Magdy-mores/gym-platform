<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                <span class="neon-accent">Edit</span> Gym Schedule Shift
            </h2>
            <a href="{{ route('admin.schedules.index') }}" class="text-xs font-bold text-gray-400 hover:text-white transition">
                &larr; Back to Schedules
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                <div class="pb-4 border-b border-white/10 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white uppercase">Edit Shift: {{ $schedule->days_label }}</h3>
                        <p class="text-xs text-gray-400">Update working days, time slots, and target gender.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $schedule->target_gender === 'men' ? 'bg-blue-500/20 text-blue-400' : 'bg-pink-500/20 text-pink-400' }}">
                        {{ $schedule->target_gender }}
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.schedules.update', $schedule->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Target Gender & Days Label -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Target Gender</label>
                            <select name="target_gender" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                                <option value="men" {{ $schedule->target_gender === 'men' ? 'selected' : '' }} class="bg-[#181a20]">Men (الرجال)</option>
                                <option value="women" {{ $schedule->target_gender === 'women' ? 'selected' : '' }} class="bg-[#181a20]">Women (السيدات)</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Days Label</label>
                            <input type="text" name="days_label" value="{{ old('days_label', $schedule->days_label) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                        </div>
                    </div>

                    <!-- Time Label & Off Day -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Shift Hours Label</label>
                            <input type="text" name="time_label" value="{{ old('time_label', $schedule->time_label) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Notes / Remarks</label>
                            <input type="text" name="notes" value="{{ old('notes', $schedule->notes) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                        </div>
                    </div>

                    <!-- Start & End Time (Optional exact times) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Start Time (Optional)</label>
                            <input type="time" name="start_time" value="{{ old('start_time', $schedule->start_time) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">End Time (Optional)</label>
                            <input type="time" name="end_time" value="{{ old('end_time', $schedule->end_time) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                        </div>
                    </div>

                    <!-- Is Off Day Checkbox -->
                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_off_day" value="1" id="is_off_day" {{ $schedule->is_off_day ? 'checked' : '' }} class="rounded bg-white/5 border-white/10 text-red-500 focus:ring-0 cursor-pointer">
                        <label for="is_off_day" class="text-xs font-bold text-gray-300 cursor-pointer">Mark as Official Day Off for this Gender</label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 border-t border-white/10 flex items-center gap-4">
                        <button type="submit" class="px-8 py-3 rounded-full bg-neon-gradient text-white font-bold text-sm bg-neon-glow cursor-pointer hover:opacity-90 transition">
                            Update Schedule Shift
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
