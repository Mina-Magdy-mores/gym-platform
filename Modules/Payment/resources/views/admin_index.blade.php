<x-app-layout>
    <x-slot name="header">
        <div class="max-w-[1700px] mx-auto flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                Admin <span class="neon-accent">Master Financial Ledger</span>
            </h2>
            <div class="px-4 py-1 rounded-full bg-neon-gradient text-white text-xs font-black uppercase tracking-widest bg-neon-glow">
                Paymob-Style Analytics & Audit Trail
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-[1700px] mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Paymob-Style Analytics Overview Cards (Horizontal Grid) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                
                <!-- Card 1: Total Gross Revenue -->
                <div class="glass-card p-6 rounded-2xl border border-white/10 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Total Gross Revenue</span>
                        <div class="w-10 h-10 rounded-xl bg-[#ff5b00]/10 border border-[#ff5b00]/20 text-[#ff5b00] flex items-center justify-center text-xl shrink-0">
                            <i class="ri-money-dollar-circle-line"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-white">
                        {{ number_format($stats['total_gross_revenue'], 2) }} <span class="text-xs font-bold neon-accent">EGP</span>
                    </div>
                    <p class="text-[11px] text-gray-400">Total volume processed across all gateways</p>
                </div>

                <!-- Card 2: Subscriptions Revenue -->
                <div class="glass-card p-6 rounded-2xl border border-blue-500/20 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Subscriptions Volume</span>
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center text-xl shrink-0">
                            <i class="ri-vip-crown-line"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-blue-400">
                        {{ number_format($stats['total_subscriptions_revenue'], 2) }} <span class="text-xs font-bold text-blue-400">EGP</span>
                    </div>
                    <p class="text-[11px] text-gray-400">Revenue from gym membership plans</p>
                </div>

                <!-- Card 3: PT Sessions Revenue -->
                <div class="glass-card p-6 rounded-2xl border border-purple-500/20 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">PT Sessions Volume</span>
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center text-xl shrink-0">
                            <i class="ri-user-star-line"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-purple-400">
                        {{ number_format($stats['total_pt_sessions_revenue'], 2) }} <span class="text-xs font-bold text-purple-400">EGP</span>
                    </div>
                    <p class="text-[11px] text-gray-400">Gross revenue from 1-on-1 sessions</p>
                </div>

                <!-- Card 4: Net Gym 15% Commission -->
                <div class="glass-card p-6 rounded-2xl border border-green-500/20 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Gym 15% Net Commission</span>
                        <div class="w-10 h-10 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 flex items-center justify-center text-xl shrink-0">
                            <i class="ri-pie-chart-2-line"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-green-400">
                        {{ number_format($stats['total_gym_commission_15pct'], 2) }} <span class="text-xs font-bold text-green-400">EGP</span>
                    </div>
                    <p class="text-[11px] text-gray-400">Net platform earnings retained from PT sessions</p>
                </div>

            </div>

            <!-- Paymob-Style Advanced Filters Bar -->
            <div class="glass-card p-6 rounded-2xl border border-white/10 space-y-4">
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="ri-filter-3-line neon-accent"></i> Advanced Transactions Filter Bar
                    </h3>
                    <a href="{{ route('admin.payments.index') }}" class="text-xs font-bold text-gray-400 hover:text-white transition">Clear All Filters</a>
                </div>

                <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                    <!-- Search Keyword -->
                    <div class="lg:col-span-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Search User / Txn ID</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or transaction ID..."
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-neon-glow">
                    </div>

                    <!-- Payment Type -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Type</label>
                        <select name="type" style="background-color: #12141a; color: #ffffff;" class="w-full border border-white/10 rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-neon-glow">
                            <option value="" style="background-color: #12141a; color: #ffffff;">All Types</option>
                            <option value="subscription" @selected(request('type') === 'subscription') style="background-color: #12141a; color: #ffffff;">Subscriptions</option>
                            <option value="pt_session" @selected(request('type') === 'pt_session') style="background-color: #12141a; color: #ffffff;">PT Sessions</option>
                        </select>
                    </div>

                    <!-- Gateway -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Gateway</label>
                        <select name="gateway" style="background-color: #12141a; color: #ffffff;" class="w-full border border-white/10 rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-neon-glow">
                            <option value="" style="background-color: #12141a; color: #ffffff;">All Gateways</option>
                            <option value="paymob" @selected(request('gateway') === 'paymob') style="background-color: #12141a; color: #ffffff;">Paymob</option>
                            <option value="stripe" @selected(request('gateway') === 'stripe') style="background-color: #12141a; color: #ffffff;">Stripe</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Status</label>
                        <select name="status" style="background-color: #12141a; color: #ffffff;" class="w-full border border-white/10 rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-neon-glow">
                            <option value="" style="background-color: #12141a; color: #ffffff;">All Statuses</option>
                            <option value="completed" @selected(request('status') === 'completed') style="background-color: #12141a; color: #ffffff;">Completed</option>
                            <option value="pending" @selected(request('status') === 'pending') style="background-color: #12141a; color: #ffffff;">Pending</option>
                            <option value="failed" @selected(request('status') === 'failed') style="background-color: #12141a; color: #ffffff;">Failed</option>
                        </select>
                    </div>

                    <!-- Submit Filter Button -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 rounded-xl bg-neon-gradient text-white font-black text-xs uppercase tracking-wider shadow-lg bg-neon-glow hover:opacity-90 transition cursor-pointer">
                            Apply Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Master Ledger Transactions Table -->
            <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <h3 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                            <i class="ri-file-list-3-line neon-accent"></i> Master Financial Audit Trail
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">Complete record of member payments, subscriptions, and trainer bookings with PDF invoice downloads</p>
                    </div>
                    <span class="text-xs font-bold text-gray-400">Total Found: {{ $payments->total() }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-white/10 text-gray-400 uppercase font-black tracking-wider">
                                <th class="py-3 px-4">Txn ID / Ref</th>
                                <th class="py-3 px-4">Customer</th>
                                <th class="py-3 px-4">Item Type</th>
                                <th class="py-3 px-4">Gateway</th>
                                <th class="py-3 px-4">Gross Amount</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Date & Time</th>
                                <th class="py-3 px-4 text-right">Tax Invoice</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 font-semibold text-gray-300">
                            @forelse($payments as $p)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="py-4 px-4 font-mono font-bold text-white" title="{{ $p->transaction_id }}">
                                        #{{ \Illuminate\Support\Str::limit($p->transaction_id, 16, '...') }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-white text-sm">{{ $p->user->name ?? 'Member' }}</div>
                                        <div class="text-[11px] text-gray-400 font-mono">{{ $p->user->email ?? '' }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($p->booking_id)
                                            @php
                                                $rawTrainerName = $p->booking->trainer->name ?? 'Trainer';
                                                $cleanTrainerName = preg_replace('/^(Coach|Captain)\s+/i', '', $rawTrainerName);
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-purple-500/20 text-purple-400 border border-purple-500/30 whitespace-nowrap inline-flex items-center gap-1">
                                                <i class="ri-user-star-line"></i> PT Session (Coach {{ $cleanTrainerName }})
                                            </span>
                                        @elseif($p->subscription_plan_id)
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30 whitespace-nowrap inline-flex items-center gap-1">
                                                <i class="ri-vip-crown-line"></i> Plan ({{ $p->plan->name ?? '' }})
                                            </span>
                                        @elseif(str_contains(json_encode($p->payload), 'FITCLUB-PT-') || str_contains(json_encode($p->payload), 'PT-'))
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-purple-500/20 text-purple-400 border border-purple-500/30 whitespace-nowrap inline-flex items-center gap-1">
                                                <i class="ri-user-star-line"></i> PT Session (Out-of-Pocket)
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-gray-500/20 text-gray-400 whitespace-nowrap inline-flex items-center gap-1">
                                                General Payment
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 font-bold uppercase text-neon-accent">{{ $p->gateway }}</td>
                                    <td class="py-4 px-4 font-black text-white text-sm">{{ number_format($p->amount, 2) }} {{ $p->currency }}</td>
                                    <td class="py-4 px-4">
                                        @if($p->status === 'completed')
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-green-500/20 text-green-400 border border-green-500/30">Completed</span>
                                        @elseif($p->status === 'pending')
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Pending</span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30">{{ $p->status }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-gray-400">{{ $p->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="py-4 px-4 text-right">
                                        @if($p->status === 'completed')
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('invoices.show', $p->id) }}" target="_blank"
                                                    class="px-2 py-1 rounded-lg bg-white/10 text-white hover:bg-white/20 transition text-[10px] font-bold inline-flex items-center gap-1">
                                                    <i class="ri-eye-line"></i> View
                                                </a>
                                                <a href="{{ route('invoices.download', $p->id) }}" target="_blank"
                                                    class="px-2.5 py-1 rounded-lg bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 hover:bg-[#ff5b00] hover:text-white transition text-[10px] font-bold inline-flex items-center gap-1">
                                                    <i class="ri-download-2-line"></i> PDF
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-gray-500 text-xs italic">No Invoice</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-gray-400 italic">
                                        No financial transactions found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="mt-4">
                    {{ $payments->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
