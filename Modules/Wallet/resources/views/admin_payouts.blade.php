<x-app-layout>
    <x-slot name="title">Trainers Treasury & Wallets Control</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                    <i class="ri-wallet-3-line text-[#ff5b00]"></i> Trainers Treasury & Wallets Control
                </h2>
                <p class="text-xs text-gray-400 mt-1">Master financial ledger: inspect individual trainer wallets, review earnings, and settle withdrawal payouts</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3.5 py-1.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 rounded-xl text-xs font-black uppercase font-mono">
                    Total Balances: {{ number_format($stats['totalTrainerBalance'] ?? 0, 2) }} EGP
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Flash Alerts -->
        @if(session('status') === 'payout_approved')
            <div class="p-4 rounded-xl bg-green-500/20 border border-green-500/30 text-green-400 text-sm font-bold flex items-center gap-2">
                <i class="ri-checkbox-circle-fill text-xl"></i>
                <span>Payout request approved and settled successfully! Ledger transaction recorded.</span>
            </div>
        @endif

        @if(session('status') === 'payout_rejected')
            <div class="p-4 rounded-xl bg-yellow-500/20 border border-yellow-500/30 text-yellow-400 text-sm font-bold flex items-center gap-2">
                <i class="ri-information-fill text-xl"></i>
                <span>Payout request rejected. Frozen balance restored back to trainer wallet.</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-500/20 border border-red-500/30 text-red-400 text-sm font-bold flex items-center gap-2">
                <i class="ri-error-warning-fill text-xl"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- 4 KPI Telemetry Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-5 rounded-2xl bg-[#12141c]/90 border border-white/10 space-y-1">
                <div class="text-[10px] text-gray-400 uppercase font-black tracking-wider">Total Coaches Earned</div>
                <div class="text-2xl font-black text-white font-mono">{{ number_format($stats['totalEarned'] ?? 0, 2) }} <span class="text-xs text-gray-400 font-sans">EGP</span></div>
                <div class="text-[10px] text-gray-400">Cumulative platform commissions</div>
            </div>
            <div class="p-5 rounded-2xl bg-[#12141c]/90 border border-emerald-500/20 space-y-1">
                <div class="text-[10px] text-emerald-400 uppercase font-black tracking-wider">Current Available Balances</div>
                <div class="text-2xl font-black text-emerald-400 font-mono">{{ number_format($stats['totalTrainerBalance'] ?? 0, 2) }} <span class="text-xs font-sans">EGP</span></div>
                <div class="text-[10px] text-gray-400">Total payable to trainers</div>
            </div>
            <div class="p-5 rounded-2xl bg-[#12141c]/90 border border-amber-500/20 space-y-1">
                <div class="text-[10px] text-amber-400 uppercase font-black tracking-wider">Pending Payouts</div>
                <div class="text-2xl font-black text-amber-400 font-mono">{{ $stats['pendingPayoutsCount'] ?? 0 }} <span class="text-xs font-sans">({{ number_format($stats['pendingPayoutsSum'] ?? 0, 2) }} EGP)</span></div>
                <div class="text-[10px] text-gray-400">Awaiting Admin transfer approval</div>
            </div>
            <div class="p-5 rounded-2xl bg-[#12141c]/90 border border-sky-500/20 space-y-1">
                <div class="text-[10px] text-sky-400 uppercase font-black tracking-wider">Settled & Transferred</div>
                <div class="text-2xl font-black text-sky-400 font-mono">{{ number_format($stats['settledPayoutsSum'] ?? 0, 2) }} <span class="text-xs font-sans">EGP</span></div>
                <div class="text-[10px] text-gray-400">Total approved withdrawals</div>
            </div>
        </div>

        <!-- 1. Coach Wallets Overview Grid -->
        <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <div>
                    <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="ri-wallet-3-line text-[#ff5b00]"></i> Individual Trainer Wallets Ledger ({{ $trainerWallets->count() }})
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Real-time balance tracking for each certified personal trainer</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($trainerWallets as $w)
                    @php
                        $coachName = preg_replace('/^(Captain|Coach)\s+/i', '', $w->user->name ?? 'Coach');
                    @endphp
                    <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 hover:border-[#ff5b00]/40 transition space-y-4 flex flex-col justify-between group">
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                @if($w->user?->getFirstMediaUrl('avatar', 'thumb'))
                                    <img src="{{ $w->user->getFirstMediaUrl('avatar', 'thumb') }}" alt="Avatar" class="w-11 h-11 rounded-xl object-cover border border-[#ff5b00] shrink-0">
                                @else
                                    <div class="w-11 h-11 rounded-xl bg-neon-gradient flex items-center justify-center font-black text-white text-base shrink-0 uppercase">
                                        {{ mb_substr($coachName, 0, 1) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="font-bold text-white text-sm truncate">{{ $w->user->name ?? 'Unknown Trainer' }}</div>
                                    <div class="text-gray-400 text-[11px] truncate">{{ $w->user->email ?? '' }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-white/5 font-mono">
                                <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/5 space-y-0.5">
                                    <span class="text-[9px] text-gray-400 font-sans uppercase font-bold block">Available Balance</span>
                                    <span class="font-black text-emerald-400 text-sm">{{ number_format($w->balance, 2) }} EGP</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/5 space-y-0.5">
                                    <span class="text-[9px] text-gray-400 font-sans uppercase font-bold block">Total Earned</span>
                                    <span class="font-black text-white text-sm">{{ number_format($w->total_earned, 2) }} EGP</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-white/5 flex items-center justify-between">
                            <span class="text-[10px] font-mono text-gray-500">Wallet #{{ $w->id }}</span>
                            @if($w->user_id)
                                <a href="{{ route('chat.show', $w->user_id) }}" class="text-[10px] font-bold text-[#ff5b00] hover:text-white transition flex items-center gap-1">
                                    <i class="ri-chat-smile-2-line"></i> Chat Coach
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-gray-400 italic">No trainer wallets initialized yet.</div>
                @endforelse
            </div>
        </div>

        <!-- 2. Payout Requests Management Table -->
        <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <div>
                    <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="ri-bank-card-line text-[#ff5b00]"></i> Withdrawal Payout Requests ({{ $payoutRequests->count() }})
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Review requested amounts, verify InstaPay / Vodafone Cash accounts, and authorize settlements</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-white/10 text-gray-400 uppercase font-black tracking-wider">
                            <th class="py-3 px-4">Request ID</th>
                            <th class="py-3 px-4">Trainer</th>
                            <th class="py-3 px-4">Requested Amount</th>
                            <th class="py-3 px-4">Method</th>
                            <th class="py-3 px-4">Account / IPA Details</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 font-semibold text-gray-300">
                        @forelse($payoutRequests as $payout)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-4 px-4 font-mono font-bold text-white">#REQ-{{ $payout->id }}</td>
                                <td class="py-4 px-4 font-bold text-white text-sm">{{ $payout->user->name ?? 'Trainer' }}</td>
                                <td class="py-4 px-4 font-black text-green-400 text-sm font-mono">{{ number_format($payout->amount, 2) }} EGP</td>
                                <td class="py-4 px-4 uppercase font-bold text-[#ff5b00]">{{ str_replace('_', ' ', $payout->payment_method) }}</td>
                                <td class="py-4 px-4 font-mono text-gray-200">
                                    <span class="bg-white/5 px-2.5 py-1 rounded-lg border border-white/10 inline-block">{{ $payout->account_details }}</span>
                                </td>
                                <td class="py-4 px-4 text-gray-400 font-mono">{{ $payout->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="py-4 px-4">
                                    @if($payout->status === 'pending')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Pending Approval</span>
                                    @elseif($payout->status === 'approved')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-green-500/20 text-green-400 border border-green-500/30">Approved & Settled</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30">Rejected</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right">
                                    @if($payout->status === 'pending')
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Approve Form -->
                                            <form action="{{ route('admin.payouts.approve', $payout->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Confirm approving and transferring {{ $payout->amount }} EGP to {{ $payout->user->name ?? 'Trainer' }}?')"
                                                    class="px-3.5 py-1.5 rounded-lg bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-green-500 hover:text-white font-black uppercase text-[10px] transition cursor-pointer">
                                                    <i class="ri-check-line"></i> Approve
                                                </button>
                                            </form>

                                            <!-- Reject Form -->
                                            <form action="{{ route('admin.payouts.reject', $payout->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Confirm rejecting this payout request? Frozen funds will be restored to trainer wallet.')"
                                                    class="px-3.5 py-1.5 rounded-lg bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500 hover:text-white font-black uppercase text-[10px] transition cursor-pointer">
                                                    <i class="ri-close-line"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-gray-500 text-xs italic">Settled</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-400 italic">No payout requests submitted yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Recent Wallet Transactions History Ledger -->
        <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <div>
                    <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="ri-history-line text-emerald-400"></i> Platform Commissions & Payouts Ledger
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Live transaction log of training commissions credited and payout debits</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-white/10 text-gray-400 uppercase font-black tracking-wider">
                            <th class="py-3 px-4">Tx ID</th>
                            <th class="py-3 px-4">Coach</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Description / Booking</th>
                            <th class="py-3 px-4">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 font-semibold text-gray-300">
                        @forelse($recentTransactions as $tx)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3 px-4 font-mono font-bold text-white">#TX-{{ $tx->id }}</td>
                                <td class="py-3 px-4 font-bold text-white">{{ $tx->wallet->user->name ?? 'Coach' }}</td>
                                <td class="py-3 px-4">
                                    @if($tx->type === 'credit')
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase bg-green-500/20 text-green-400 border border-green-500/30">Commission Credit</span>
                                    @elseif($tx->type === 'debit')
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase bg-red-500/20 text-red-400 border border-red-500/30">Payout Debit</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">{{ $tx->type }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-mono font-black text-sm {{ $tx->type === 'credit' ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $tx->type === 'credit' ? '+' : '-' }}{{ number_format($tx->amount, 2) }} EGP
                                </td>
                                <td class="py-3 px-4 text-gray-300">
                                    <div>{{ $tx->description }}</div>
                                    @if($tx->booking)
                                        <div class="text-[10px] text-gray-500 font-mono">Session for: {{ $tx->booking->user->name ?? 'Member' }} (#{{ $tx->booking->id }})</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-400 font-mono">{{ $tx->created_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400 italic">No wallet transactions recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
