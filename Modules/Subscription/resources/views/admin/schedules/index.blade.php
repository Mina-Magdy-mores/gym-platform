<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                <span class="neon-accent">Admin</span> Gym Schedules
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.plans.index') }}" class="px-4 py-2 rounded-full glass-card border border-white/10 text-xs font-bold text-gray-300 hover:border-[#ff5b00] transition">
                    &larr; Back to Plans
                </a>
                <a href="{{ route('admin.schedules.create') }}" class="px-5 py-2.5 rounded-full bg-neon-gradient text-white text-xs font-bold uppercase shadow-lg hover:opacity-90 transition">
                    + Create New Schedule
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Status -->
            @if(session('status') === 'schedule-created')
                <div class="p-4 rounded-xl glass-card border border-green-500/30 text-green-400 font-bold text-sm">
                    New gym schedule created successfully!
                </div>
            @endif

            @if(session('status') === 'schedule-updated')
                <div class="p-4 rounded-xl glass-card border border-green-500/30 text-green-400 font-bold text-sm">
                    Gym schedule updated successfully!
                </div>
            @endif

            @if(session('status') === 'schedule-deleted')
                <div class="p-4 rounded-xl glass-card border border-red-500/30 text-red-400 font-bold text-sm">
                    Gym schedule deleted successfully!
                </div>
            @endif

            <div class="glass-card p-6 rounded-2xl border border-white/5 space-y-6">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <h3 class="text-xl font-bold text-white uppercase">Operating Hours & Shifts</h3>
                    <span class="text-xs text-gray-400 font-bold">{{ $schedules->count() }} Shifts</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-gray-300">
                        <thead class="bg-white/5 uppercase text-gray-400 font-bold border-b border-white/10">
                            <tr>
                                <th class="p-3">Target Gender</th>
                                <th class="p-3">Days Label</th>
                                <th class="p-3">Shift Hours</th>
                                <th class="p-3">Type / Status</th>
                                <th class="p-3">Notes</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($schedules as $sch)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="p-3">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $sch->target_gender === 'men' ? 'bg-blue-500/20 text-blue-400' : 'bg-pink-500/20 text-pink-400' }}">
                                            {{ $sch->target_gender }}
                                        </span>
                                    </td>
                                    <td class="p-3 font-bold text-white text-sm">{{ $sch->days_label }}</td>
                                    <td class="p-3 font-black text-[#ff5b00] text-xs">{{ $sch->time_label }}</td>
                                    <td class="p-3">
                                        @if($sch->is_off_day)
                                            <span class="px-2.5 py-1 rounded-full bg-red-500/20 text-red-400 text-[10px] font-black uppercase">
                                                Day Off
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-green-500/20 text-green-400 text-[10px] font-black uppercase">
                                                Active Shift
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-gray-400">{{ $sch->notes ?? '-' }}</td>
                                    <td class="p-3 text-right space-x-2">
                                        <a href="{{ route('admin.schedules.edit', $sch->id) }}" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-[#ff5b00] text-white font-bold transition">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.schedules.destroy', $sch->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this schedule shift?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500 hover:text-white font-bold transition cursor-pointer">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
