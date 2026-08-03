<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                <span class="neon-accent">Edit</span> Subscription Plan
            </h2>
            <a href="{{ route('admin.plans.index') }}" class="text-xs font-bold text-gray-400 hover:text-white transition">
                &larr; Back to Plans
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                <div class="pb-4 border-b border-white/10 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white uppercase">Edit Plan: {{ $plan->name }}</h3>
                        <p class="text-xs text-gray-400">Update subscription plan details, pricing, and benefits.</p>
                    </div>
                    @if($plan->is_featured)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30">
                            ★ FEATURED OFFER
                        </span>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.plans.update', $plan->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Name & Price -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2 space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Plan Name</label>
                            <input type="text" name="name" value="{{ old('name', $plan->name) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Price (EGP)</label>
                            <input type="number" name="price" value="{{ old('price', (int)$plan->price) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                        </div>
                    </div>

                    <!-- Duration & Description -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Duration (Months)</label>
                            <input type="number" name="duration_months" value="{{ old('duration_months', $plan->duration_months) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                        </div>
                        <div class="sm:col-span-2 space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Description</label>
                            <input type="text" name="description" value="{{ old('description', $plan->description) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                        </div>
                    </div>

                    <!-- Benefits Grid -->
                    <div class="pt-4 border-t border-white/10 space-y-4">
                        <h4 class="text-sm font-bold text-white uppercase">Subscription Benefits</h4>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-gray-400">Free Days</label>
                                <input type="number" name="free_days" value="{{ old('free_days', $plan->free_days) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-gray-400">Freeze Days</label>
                                <input type="number" name="freeze_days" value="{{ old('freeze_days', $plan->freeze_days) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-gray-400">Invitations Count</label>
                                <input type="number" name="invitations_count" value="{{ old('invitations_count', $plan->invitations_count) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-gray-400">InBody Scans</label>
                                <input type="number" name="inbody_scans" value="{{ old('inbody_scans', $plan->inbody_scans) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-gray-400">P.T. Sessions</label>
                                <input type="number" name="pt_sessions" value="{{ old('pt_sessions', $plan->pt_sessions) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-gray-400">Kickboxing Classes</label>
                                <input type="number" name="kickboxing_classes" value="{{ old('kickboxing_classes', $plan->kickboxing_classes) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-gray-400">Nutrition Plans</label>
                                <input type="number" name="nutrition_plans" value="{{ old('nutrition_plans', $plan->nutrition_plans) }}" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                            </div>
                            <div class="flex items-center gap-2 pt-5">
                                <input type="checkbox" name="spa_access" value="1" id="spa_access" {{ $plan->spa_access ? 'checked' : '' }} class="rounded bg-white/5 border-white/10 text-[#ff5b00] focus:ring-0 cursor-pointer">
                                <label for="spa_access" class="text-xs font-bold text-gray-300 cursor-pointer">Full SPA Access</label>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Offer Checkbox -->
                    <div class="pt-4 border-t border-white/10 flex items-center gap-3">
                        <input type="checkbox" name="is_featured" value="1" id="is_featured" {{ $plan->is_featured ? 'checked' : '' }} class="rounded bg-white/5 border-white/10 text-[#ff5b00] focus:ring-0 cursor-pointer">
                        <div>
                            <label for="is_featured" class="text-xs font-black uppercase text-[#ff5b00] cursor-pointer">
                                ★ Mark as Featured Offer (Displays Glowing BEST VALUE Badge)
                            </label>
                            <p class="text-[11px] text-gray-400">Highlight this plan on the public website and mobile apps as a special recommended offer.</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 border-t border-white/10 flex items-center gap-4">
                        <button type="submit" class="px-8 py-3 rounded-full bg-neon-gradient text-white font-bold text-sm bg-neon-glow cursor-pointer hover:opacity-90 transition">
                            Update Subscription Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
