<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                Membership <span class="neon-accent">Checkout & Terms Consent</span>
            </h2>
            <div class="px-4 py-1 rounded-full bg-neon-gradient text-white text-xs font-black uppercase tracking-widest bg-neon-glow">
                Step 1 of 2: Contract Consent
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8" x-data="{ agreed: false }">
            
            <!-- Plan Summary Banner -->
            <div class="glass-card p-8 rounded-2xl border border-white/10 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-3 text-center md:text-left">
                    <div class="inline-block px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-gray-300">
                        Selected Package Review
                    </div>
                    <h3 class="text-3xl font-black uppercase tracking-wide">
                        {{ $plan->name }}
                    </h3>
                    <p class="text-gray-400 text-sm max-w-lg leading-relaxed">
                        {{ $plan->duration_months }} Months Full Access Membership Package. Please review your plan details and consent to the gym regulations below.
                    </p>
                </div>

                <div class="text-center md:text-right shrink-0 bg-white/5 p-6 rounded-2xl border border-white/10">
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Total Price</span>
                    <div class="text-4xl font-black neon-accent mt-1">
                        {{ number_format($plan->price, 2) }} <span class="text-sm font-bold text-gray-400">EGP</span>
                    </div>
                    <span class="text-[10px] text-gray-400 block mt-1">Includes All Applicable Gym Fees</span>
                </div>
            </div>

            <!-- Package Breakdown & Included Benefits Grid -->
            <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                <h4 class="text-lg font-black text-white uppercase tracking-wide flex items-center gap-2 border-b border-white/10 pb-4">
                    <i class="ri-checkbox-circle-line neon-accent"></i> Included Membership Benefits
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs font-bold text-gray-300">
                    @if($plan->free_days > 0)
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 flex items-center gap-3">
                            <i class="ri-calendar-check-fill text-[#ff5b00] text-xl"></i>
                            <div>
                                <span class="block text-white">{{ $plan->free_days }} Days</span>
                                <span class="text-gray-400 text-[10px]">Free Bonus Extension</span>
                            </div>
                        </div>
                    @endif

                    @if($plan->freeze_days > 0)
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 flex items-center gap-3">
                            <i class="ri-snowflake-fill text-blue-400 text-xl"></i>
                            <div>
                                <span class="block text-white">{{ $plan->freeze_days }} Days</span>
                                <span class="text-gray-400 text-[10px]">Freeze Allowance</span>
                            </div>
                        </div>
                    @endif

                    @if($plan->invitations_count > 0)
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 flex items-center gap-3">
                            <i class="ri-user-add-fill text-purple-400 text-xl"></i>
                            <div>
                                <span class="block text-white">{{ $plan->invitations_count }} Passes</span>
                                <span class="text-gray-400 text-[10px]">Guest Invitations</span>
                            </div>
                        </div>
                    @endif

                    @if($plan->inbody_scans > 0)
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 flex items-center gap-3">
                            <i class="ri-scales-3-line text-green-400 text-xl"></i>
                            <div>
                                <span class="block text-white">{{ $plan->inbody_scans }} Scans</span>
                                <span class="text-gray-400 text-[10px]">InBody Body Composition</span>
                            </div>
                        </div>
                    @endif

                    @if($plan->pt_sessions > 0)
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 flex items-center gap-3">
                            <i class="ri-user-star-fill text-orange-400 text-xl"></i>
                            <div>
                                <span class="block text-white">{{ $plan->pt_sessions }} Sessions</span>
                                <span class="text-gray-400 text-[10px]">Personal Trainer (P.T)</span>
                            </div>
                        </div>
                    @endif

                    @if($plan->spa_access)
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 flex items-center gap-3">
                            <i class="ri-sparkling-fill text-yellow-400 text-xl"></i>
                            <div>
                                <span class="block text-white">Unlimited</span>
                                <span class="text-gray-400 text-[10px]">SPA & Steam Access</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Mandatory Gym Terms Consent Box -->
            <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                <div class="border-b border-white/10 pb-4">
                    <h4 class="text-lg font-black text-white uppercase tracking-wide flex items-center gap-2">
                        <i class="ri-file-shield-line neon-accent"></i> Gym Regulations Agreement
                    </h4>
                    <p class="text-xs text-gray-400 mt-1">You must read and agree to all 16 gym rules before completing subscription</p>
                </div>

                <!-- Scrollable Regulations Box -->
                <div class="max-h-60 overflow-y-auto space-y-3 pr-2 scrollbar-thin scrollbar-thumb-[#ff5b00]/30 text-xs text-gray-300 leading-relaxed">
                    @foreach($gymRules as $rule)
                        <div class="p-3 rounded-xl bg-white/5 border border-white/5 flex items-start gap-3">
                            <span class="px-2 py-0.5 rounded bg-neon-gradient text-white font-black text-[10px] shrink-0 mt-0.5">
                                #{{ $rule->rule_number }}
                            </span>
                            <p>{{ $rule->rule_text }}</p>
                        </div>
                    @endforeach
                </div>

                <!-- Agreement Form -->
                <form method="POST" action="{{ route('subscriptions.store') }}" class="pt-4 border-t border-white/10 space-y-6">
                    @csrf
                    <input type="hidden" name="subscription_plan_id" value="{{ $plan->id }}">

                    <label class="flex items-center gap-3 cursor-pointer p-4 rounded-xl bg-white/5 border border-white/10 hover:border-[#ff5b00]/50 transition">
                        <input type="checkbox" x-model="agreed" required class="rounded bg-white/10 border-white/20 text-[#ff5b00] focus:ring-[#ff5b00] w-5 h-5">
                        <span class="text-xs font-bold text-white">
                            I have read, understood, and agree to all 16 Gym Terms & Regulations above
                        </span>
                    </label>

                    <div class="flex items-center justify-between gap-4 pt-2">
                        <a href="{{ url('/#plans') }}" class="px-6 py-3 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition">
                            Back to Plans
                        </a>

                        <button type="submit" 
                                :disabled="!agreed" 
                                :class="agreed ? 'bg-neon-gradient bg-neon-glow hover:opacity-90 cursor-pointer' : 'bg-gray-700 opacity-50 cursor-not-allowed'" 
                                class="px-8 py-3.5 rounded-full text-white font-black text-sm uppercase tracking-wider transition duration-300">
                            Confirm Contract & Proceed to Payment
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
