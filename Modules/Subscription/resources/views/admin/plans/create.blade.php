<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                <span class="neon-accent">Create</span> New Subscription Plan
            </h2>
            <a href="{{ route('admin.plans.index') }}" class="text-xs font-bold text-gray-400 hover:text-white transition">
                &larr; Back to Plans
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                <div class="pb-4 border-b border-white/10">
                    <h3 class="text-xl font-bold text-white uppercase">Plan Details & Pricing</h3>
                    <p class="text-xs text-gray-400">Fill in the subscription plan information and benefits.</p>
                </div>

                <form method="POST" action="{{ route('admin.plans.store') }}" class="space-y-6">
                    @csrf

                    <!-- Name & Price -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2 space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Plan Name</label>
                            <input type="text" name="name" placeholder="e.g. 15 Month Platinum VIP" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Price (EGP)</label>
                            <input type="number" name="price" placeholder="4000" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                        </div>
                    </div>

                    <!-- Duration & Description -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Duration (Months)</label>
                            <input type="number" name="duration_months" placeholder="15" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]" required>
                        </div>
                        <div class="sm:col-span-2 space-y-1">
                            <label class="block text-xs font-bold text-gray-300">Description</label>
                            <input type="text" name="description" placeholder="Platinum Membership Package" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                        </div>
                    </div>

                    <!-- Benefits Grid -->
                    <div class="pt-4 border-t border-white/10 space-y-4">
                        <h4 class="text-sm font-bold text-white uppercase">Subscription Benefits</h4>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-gray-400">Free Days</label>
                                <input type="number" name="free_days" value="0" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-gray-400">Freeze Days</label>
                                <input type="number" name="freeze_days" value="0" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-gray-400">Invitations Count</label>
                                <input type="number" name="invitations_count" value="0" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-gray-400">InBody Scans</label>
                                <input type="number" name="inbody_scans" value="0" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-gray-400">P.T. Sessions</label>
                                <input type="number" name="pt_sessions" value="0" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-gray-400">Kickboxing Classes</label>
                                <input type="number" name="kickboxing_classes" value="0" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-bold text-gray-400">Nutrition Plans</label>
                                <input type="number" name="nutrition_plans" value="0" class="w-full bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-[#ff5b00]">
                            </div>
                            <div class="flex items-center gap-2 pt-5">
                                <input type="checkbox" name="spa_access" value="1" id="spa_access" class="rounded bg-white/5 border-white/10 text-[#ff5b00] focus:ring-0 cursor-pointer" checked>
                                <label for="spa_access" class="text-xs font-bold text-gray-300 cursor-pointer">Full SPA Access</label>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Offer Checkbox -->
                    <div class="pt-4 border-t border-white/10 flex items-center gap-3">
                        <input type="checkbox" name="is_featured" value="1" id="is_featured" class="rounded bg-white/5 border-white/10 text-[#ff5b00] focus:ring-0 cursor-pointer">
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
                            Save Subscription Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
