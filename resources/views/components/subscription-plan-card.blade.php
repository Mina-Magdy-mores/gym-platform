@props(['plan', 'showSubscribeForm' => false])

<div class="glass-card rounded-2xl p-6 border border-white/5 hover:border-[#ff5b00]/50 transition duration-300 flex flex-col justify-between relative group {{ $plan->is_featured ? 'border-2 border-[#ff5b00] shadow-[0_0_30px_rgba(255,91,0,0.25)] bg-white/[0.04]' : '' }}">
    
    <!-- Featured Badge -->
    @if($plan->is_featured)
        <div class="absolute -top-3.5 right-6 px-3 py-1 rounded-full bg-neon-gradient text-white text-[10px] font-black uppercase tracking-wider shadow-lg bg-neon-glow flex items-center gap-1 z-20">
            <i class="ri-star-fill text-xs"></i> BEST VALUE OFFER
        </div>
    @endif

    <!-- Background Glow -->
    <div class="absolute -top-10 -right-10 w-24 h-24 bg-[#ff5b00]/10 rounded-full blur-2xl group-hover:bg-[#ff5b00]/20 transition rounded-tr-2xl overflow-hidden pointer-events-none"></div>

    <div class="space-y-4 relative z-10">
        <div class="text-center pb-4 border-b border-white/10">
            <h4 class="text-2xl font-black text-white uppercase tracking-wide">{{ $plan->name }}</h4>
            <div class="mt-2 flex items-baseline justify-center gap-1">
                <span class="text-4xl font-black neon-accent">{{ (int)$plan->price }}</span>
                <span class="text-sm font-bold text-gray-400">EGP</span>
            </div>
            <p class="text-xs text-gray-400 mt-1 font-bold">{{ $plan->duration_months }} Months Package</p>
        </div>

        <!-- Benefits List -->
        <ul class="space-y-2.5 text-xs text-gray-300 font-semibold">
            @if($plan->free_days > 0)
                <li class="flex items-center gap-2"><i class="ri-calendar-check-fill text-[#ff5b00]"></i> {{ $plan->free_days }} Days Free Extension</li>
            @endif
            @if($plan->freeze_days > 0)
                <li class="flex items-center gap-2"><i class="ri-snowflake-fill text-[#ff5b00]"></i> {{ $plan->freeze_days }} Days Freeze Allowance</li>
            @endif
            @if($plan->invitations_count > 0)
                <li class="flex items-center gap-2"><i class="ri-user-add-fill text-[#ff5b00]"></i> {{ $plan->invitations_count }} Guest Invitations</li>
            @endif
            @if($plan->inbody_scans > 0)
                <li class="flex items-center gap-2"><i class="ri-body-scan-fill text-[#ff5b00]"></i> {{ $plan->inbody_scans }} InBody Scans</li>
            @endif
            @if($plan->pt_sessions > 0)
                <li class="flex items-center gap-2"><i class="ri-heart-pulse-fill text-[#ff5b00]"></i> {{ $plan->pt_sessions }} P.T. Sessions</li>
            @endif
            @if($plan->kickboxing_classes > 0)
                <li class="flex items-center gap-2"><i class="ri-boxing-fill text-[#ff5b00]"></i> {{ $plan->kickboxing_classes }} Kickboxing Classes</li>
            @endif
            @if($plan->nutrition_plans > 0)
                <li class="flex items-center gap-2"><i class="ri-restaurant-2-fill text-[#ff5b00]"></i> {{ $plan->nutrition_plans }} Nutrition Plans</li>
            @endif
            @if($plan->spa_access)
                <li class="flex items-center gap-2"><i class="ri-sparkling-fill text-yellow-400"></i> Unlimited SPA Access</li>
            @endif
        </ul>
    </div>

    <div class="mt-6 pt-4 border-t border-white/5 relative z-10">
        @if(auth()->check() && auth()->user()->hasAnyRole(['trainer', 'admin', 'super-admin']))
            <div class="block w-full text-center py-3 rounded-xl text-xs font-bold uppercase tracking-wider bg-white/5 border border-white/10 text-gray-400 cursor-not-allowed select-none">
                <i class="ri-shield-user-line mr-1 text-[#ff5b00]"></i> Trainer / Staff View Mode
            </div>
        @else
            <a href="{{ route('checkout.show', $plan->id) }}" class="block w-full text-center py-3 rounded-xl text-xs font-black uppercase tracking-wider transition duration-200 shadow-md cursor-pointer {{ $plan->is_featured ? 'bg-neon-gradient text-white hover:opacity-90 bg-neon-glow' : 'bg-white/10 hover:bg-[#ff5b00] text-white' }}">
                Subscribe Now
            </a>
        @endif
    </div>
</div>
