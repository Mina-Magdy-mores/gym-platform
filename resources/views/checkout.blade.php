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
            
            <!-- Plan Action Dynamic Notice Banners -->
            @if(isset($prep) && $prep['action'] === 'upgrade')
                <div class="p-4 rounded-xl bg-gradient-to-r from-[#ff5b00]/20 to-amber-500/20 border border-[#ff5b00]/40 flex items-center justify-between text-xs text-white">
                    <div class="flex items-center gap-3">
                        <span class="p-2 rounded-lg bg-neon-gradient text-white text-lg font-black">
                            <i class="ri-vip-crown-line"></i>
                        </span>
                        <div>
                            <span class="font-black uppercase text-[#ff5b00] block text-sm">7-Day Plan Upgrade Window</span>
                            <span class="text-gray-300">You are upgrading to a higher tier plan during your first 7 days. You pay only the price difference!</span>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-neon-gradient text-white font-black uppercase tracking-wider text-[10px]">
                        Price Difference Applied
                    </span>
                </div>
            @elseif(isset($prep) && $prep['action'] === 'queued')
                <div class="p-4 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-between text-xs text-white">
                    <div class="flex items-center gap-3">
                        <span class="p-2 rounded-lg bg-blue-500/20 text-blue-400 text-lg font-black">
                            <i class="ri-time-line"></i>
                        </span>
                        <div>
                            <span class="font-black uppercase text-blue-400 block text-sm">Queued Future Membership</span>
                            <span class="text-gray-300">Your current membership remains active. This new plan will start automatically once your active plan ends.</span>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 font-black uppercase tracking-wider text-[10px]">
                        Queued Subscription
                    </span>
                </div>
            @endif

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
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">
                        {{ (isset($prep) && $prep['action'] === 'upgrade') ? 'Price Difference Due' : 'Total Price Due' }}
                    </span>
                    <div class="text-4xl font-black neon-accent mt-1">
                        {{ number_format(isset($prep) ? $prep['amount_to_pay'] : $plan->price, 2) }} <span class="text-sm font-bold text-gray-400">EGP</span>
                    </div>
                    @if(isset($prep) && $prep['action'] === 'upgrade')
                        <span class="text-[10px] text-gray-400 line-through block mt-1">Original Price: {{ number_format($plan->price, 2) }} EGP</span>
                    @else
                        <span class="text-[10px] text-gray-400 block mt-1">Includes All Applicable Gym Fees</span>
                    @endif
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
                                <span class="block text-white">{{ $plan->invitations_count }} Guests</span>
                                <span class="text-gray-400 text-[10px]">Free Guest Invitations</span>
                            </div>
                        </div>
                    @endif

                    @if($plan->inbody_scans > 0)
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 flex items-center gap-3">
                            <i class="ri-scales-3-fill text-green-400 text-xl"></i>
                            <div>
                                <span class="block text-white">{{ $plan->inbody_scans }} Scans</span>
                                <span class="text-gray-400 text-[10px]">InBody Body Composition</span>
                            </div>
                        </div>
                    @endif

                    @if($plan->pt_sessions > 0)
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 flex items-center gap-3">
                            <i class="ri-user-star-fill text-[#ff5b00] text-xl"></i>
                            <div>
                                <span class="block text-white">{{ $plan->pt_sessions }} Sessions</span>
                                <span class="text-gray-400 text-[10px]">Personal Trainer (P.T)</span>
                            </div>
                        </div>
                    @endif

                    @if($plan->kickboxing_classes > 0)
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 flex items-center gap-3">
                            <i class="ri-boxing-fill text-red-400 text-xl"></i>
                            <div>
                                <span class="block text-white">{{ $plan->kickboxing_classes }} Classes</span>
                                <span class="text-gray-400 text-[10px]">Kickboxing Sessions</span>
                            </div>
                        </div>
                    @endif

                    @if($plan->nutrition_plans > 0)
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 flex items-center gap-3">
                            <i class="ri-restaurant-fill text-teal-400 text-xl"></i>
                            <div>
                                <span class="block text-white">{{ $plan->nutrition_plans }} Diets</span>
                                <span class="text-gray-400 text-[10px]">Custom Nutrition Plans</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Mandatory Gym Terms & Regulations Consent Table -->
            <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <h4 class="text-lg font-black text-white uppercase tracking-wide flex items-center gap-2">
                            <i class="ri-shield-check-line neon-accent"></i> Official Gym Regulations & Terms
                        </h4>
                        <p class="text-xs text-gray-400 mt-1">You must read and accept all 16 gym rules before proceeding to payment.</p>
                    </div>
                    <span class="text-xs font-bold text-gray-400">16 Regulations Listed</span>
                </div>

                <!-- Scrollable Terms Table -->
                <div class="max-h-72 overflow-y-auto pr-2 space-y-3 custom-scrollbar">
                    @foreach($gymRules as $rule)
                        <div class="p-3.5 rounded-xl bg-white/5 border border-white/5 flex items-start gap-3 text-xs text-gray-300 hover:border-white/10 transition">
                            <span class="px-2 py-0.5 rounded bg-neon-gradient text-white font-black text-[10px] shrink-0 mt-0.5">
                                #{{ $rule->rule_number }}
                            </span>
                            <p class="leading-relaxed">{{ $rule->rule_text }}</p>
                        </div>
                    @endforeach
                </div>

                <!-- Mandatory Checkbox Agreement Form -->
                <form method="POST" action="{{ route('subscriptions.store') }}" class="pt-6 border-t border-white/10 space-y-6">
                    @csrf
                    <input type="hidden" name="subscription_plan_id" value="{{ $plan->id }}">

                    <label class="flex items-start gap-3 cursor-pointer group p-4 rounded-xl bg-white/5 border border-white/10 hover:border-[#ff5b00]/40 transition">
                        <input type="checkbox" x-model="agreed" class="mt-1 rounded bg-white/10 border-white/20 text-[#ff5b00] focus:ring-[#ff5b00] focus:ring-offset-0 cursor-pointer">
                        <div class="text-xs leading-relaxed text-gray-300 group-hover:text-white transition">
                            <span class="font-bold text-white block mb-0.5">I have read, understood, and agree to all 16 Gym Terms & Regulations.</span>
                            I acknowledge that violating any gym rules will lead to immediate contract termination without refund.
                        </div>
                    </label>

                    <div class="flex items-center justify-between gap-4">
                        <a href="{{ url('/#plans') }}" class="px-6 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-white font-bold text-xs uppercase tracking-wider transition">
                            Back to Plans
                        </a>

                        <button type="submit" :disabled="!agreed" :class="agreed ? 'bg-neon-gradient text-white hover:opacity-90 bg-neon-glow cursor-pointer' : 'bg-white/10 text-gray-500 cursor-not-allowed'" class="px-8 py-3.5 rounded-xl font-black text-xs uppercase tracking-wider transition duration-200 shadow-xl flex items-center gap-2">
                            <span>Confirm Contract & Proceed to Payment</span>
                            <i class="ri-arrow-right-line"></i>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
