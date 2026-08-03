<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                <span class="neon-accent">Create</span> New Gym Schedule Shift
            </h2>
            <a href="{{ route('admin.schedules.index') }}" class="text-xs font-bold text-gray-400 hover:text-white transition">
                &larr; Back to Schedules
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                <div class="pb-4 border-b border-white/10">
                    <h3 class="text-xl font-bold text-white uppercase">Schedule Shift Details</h3>
                    <p class="text-xs text-gray-400">Define working days, time slots, and target gender.</p>
                </div>

                <form method="POST" action="{{ route('admin.schedules.store') }}" class="space-y-6">
                    @csrf

                    <!-- Target Gender & Days Label -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Target Gender</label>
                            <select name="target_gender" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                                <option value="men" class="bg-[#181a20]">Men (الرجال)</option>
                                <option value="women" class="bg-[#181a20]">Women (السيدات)</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Days Label</label>
                            <input type="text" name="days_label" placeholder="e.g. Saturday, Monday, and Wednesday" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                        </div>
                    </div>

                    <!-- Time Label & Off Day -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Shift Hours Label</label>
                            <input type="text" name="time_label" placeholder="e.g. 8:00 AM - 1:00 PM" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Notes / Remarks</label>
                            <input type="text" name="notes" placeholder="e.g. Morning Shift" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                        </div>
                    </div>

                    <!-- Start & End Time (Optional exact times) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Start Time (Optional)</label>
                            <input type="time" name="start_time" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">End Time (Optional)</label>
                            <input type="time" name="end_time" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                        </div>
                    </div>

                    <!-- Is Off Day Checkbox -->
                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_off_day" value="1" id="is_off_day" class="rounded bg-white/5 border-white/10 text-red-500 focus:ring-0 cursor-pointer">
                        <label for="is_off_day" class="text-xs font-bold text-gray-300 cursor-pointer">Mark as Official Day Off for this Gender</label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 border-t border-white/10 flex items-center gap-4">
                        <button type="submit" class="px-8 py-3 rounded-full bg-neon-gradient text-white font-bold text-sm bg-neon-glow cursor-pointer hover:opacity-90 transition">
                            Save Schedule Shift
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
