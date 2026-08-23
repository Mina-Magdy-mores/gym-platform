<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                <span class="neon-accent">Admin</span> Subscription Plans
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2 rounded-full glass-card border border-white/10 text-xs font-bold text-gray-300 hover:border-[#ff5b00] transition">
                    Manage Schedules
                </a>
                <a href="{{ route('admin.plans.create') }}" class="px-5 py-2.5 rounded-full bg-neon-gradient text-white text-xs font-bold uppercase shadow-lg hover:opacity-90 transition">
                    + Create New Plan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Status -->
            @if(session('status') === 'plan-created')
                <div class="p-4 rounded-xl glass-card border border-green-500/30 text-green-400 font-bold text-sm">
                    New subscription plan created successfully!
                </div>
            @endif

            @if(session('status') === 'plan-updated')
                <div class="p-4 rounded-xl glass-card border border-green-500/30 text-green-400 font-bold text-sm">
                    Subscription plan details updated successfully!
                </div>
            @endif

            @if(session('status') === 'plan-toggled')
                <div class="p-4 rounded-xl glass-card border border-yellow-500/30 text-yellow-400 font-bold text-sm">
                    Subscription plan active status updated successfully!
                </div>
            @endif

            @if(session('status') === 'plan-deleted')
                <div class="p-4 rounded-xl glass-card border border-red-500/30 text-red-400 font-bold text-sm">
                    Subscription plan deleted successfully!
                </div>
            @endif

            <div class="glass-card p-6 rounded-2xl border border-white/5 space-y-6">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <h3 class="text-xl font-bold text-white uppercase">All Subscription Plans</h3>
                    <span class="text-xs text-gray-400 font-bold">{{ $plans->count() }} Plans</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-gray-300">
                        <thead class="bg-white/5 uppercase text-gray-400 font-bold border-b border-white/10">
                            <tr>
                                <th class="p-3">Plan Name</th>
                                <th class="p-3">Duration</th>
                                <th class="p-3">Price</th>
                                <th class="p-3">Benefits</th>
                                <th class="p-3">Badge / Featured</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($plans as $pl)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="p-3 font-bold text-white text-sm">
                                        {{ $pl->name }}
                                    </td>
                                    <td class="p-3 font-bold">{{ $pl->duration_months }} Months</td>
                                    <td class="p-3 font-black text-[#ff5b00] text-sm">{{ (int)$pl->price }} EGP</td>
                                    <td class="p-3 text-gray-400">
                                        {{ $pl->free_days }} Free Days, {{ $pl->freeze_days }} Freeze Days, {{ $pl->inbody_scans }} InBody
                                    </td>
                                    <td class="p-3 whitespace-nowrap">
                                        @if($pl->is_featured)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 whitespace-nowrap shadow-sm">
                                                <i class="ri-star-fill text-[#ff5b00] text-xs"></i> Featured Offer
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-gray-400 text-[10px] font-bold bg-white/5 border border-white/5 whitespace-nowrap">Standard</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $pl->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                            {{ $pl->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-right space-x-2">
                                        <a href="{{ route('admin.plans.edit', $pl->id) }}" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-[#ff5b00] text-white font-bold transition">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.plans.toggle', $pl->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ $pl->is_active ? 'bg-yellow-500/20 text-yellow-400 hover:bg-yellow-500 hover:text-white' : 'bg-green-500/20 text-green-400 hover:bg-green-500 hover:text-white' }}">
                                                {{ $pl->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.plans.destroy', $pl->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this plan?');">
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
