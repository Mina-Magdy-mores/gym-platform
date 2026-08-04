@php
    $gymRules = \Modules\Subscription\Models\GymRule::getActiveRules();
@endphp

<div x-data="{ agreed: false }" 
     x-show="showGymTerms" 
     x-cloak 
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
    
    <div @click.away="showGymTerms = false" class="glass-card max-w-2xl w-full p-8 rounded-2xl border border-white/10 shadow-2xl relative space-y-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div class="flex items-center gap-3">
                <span class="p-2.5 rounded-xl bg-neon-gradient text-white text-xl">
                    <i class="ri-file-text-line"></i>
                </span>
                <div>
                    <h3 class="text-xl font-black text-white uppercase tracking-wider">
                        Gym Terms & Regulations
                    </h3>
                    <p class="text-xs text-gray-400">Please review and accept our gym rules before proceeding</p>
                </div>
            </div>
            <button @click="showGymTerms = false" class="text-gray-400 hover:text-white text-2xl transition">
                <i class="ri-close-line"></i>
            </button>
        </div>

        <!-- Scrollable Rules List -->
        <div class="max-h-72 overflow-y-auto space-y-3 pr-2 scrollbar-thin scrollbar-thumb-[#ff5b00]/30 text-xs text-gray-300 leading-relaxed">
            @foreach($gymRules as $rule)
                <div class="p-3 rounded-xl bg-white/5 border border-white/5 flex items-start gap-3">
                    <span class="px-2 py-0.5 rounded bg-neon-gradient text-white font-black text-[10px] shrink-0">
                        #{{ $rule->rule_number }}
                    </span>
                    <p>{{ $rule->rule_text }}</p>
                </div>
            @endforeach

            <!-- Contract Cancellation Warning -->
            <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 font-bold text-xs flex items-center gap-2">
                <i class="ri-error-warning-line text-lg shrink-0"></i>
                <span>Violation of any gym rules and regulations will result in immediate contract cancellation without refund.</span>
            </div>
        </div>

        <!-- Consent Checkbox & Action Button -->
        <div class="pt-4 border-t border-white/10 space-y-4">
            <label class="flex items-center gap-3 cursor-pointer text-xs font-bold text-gray-200">
                <input type="checkbox" x-model="agreed" class="rounded bg-white/10 border-white/20 text-[#ff5b00] focus:ring-[#ff5b00]">
                <span>I have read, understood, and agree to all Gym Terms & Regulations</span>
            </label>

            <div class="flex justify-end gap-3">
                <button @click="showGymTerms = false" type="button" class="px-5 py-2.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition">
                    Cancel
                </button>
                <button :disabled="!agreed" 
                        :class="agreed ? 'bg-neon-gradient bg-neon-glow hover:opacity-90 cursor-pointer' : 'bg-[#181a20] opacity-50 cursor-not-allowed'" 
                        type="button" 
                        @click="showGymTerms = false;" 
                        class="px-6 py-2.5 rounded-full text-white font-bold text-xs transition">
                    Confirm & Proceed
                </button>
            </div>
        </div>
    </div>
</div>
