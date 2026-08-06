<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                Trainer <span class="neon-accent">Earnings Wallet</span>
            </h2>
            <div class="px-4 py-1 rounded-full bg-neon-gradient text-white text-xs font-black uppercase tracking-widest bg-neon-glow">
                15% Platform Commission Applied
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Trainer Wallet Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Card 1: Available Balance -->
                <div class="glass-card p-8 rounded-2xl border border-white/10 relative overflow-hidden flex items-center justify-between">
                    <div class="space-y-2">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Available Balance</span>
                        <div class="text-4xl font-black text-white">
                            {{ number_format($wallet->balance, 2) }} <span class="text-sm font-bold neon-accent">EGP</span>
                        </div>
                        <p class="text-xs text-gray-400">Net earnings available for payout withdrawal</p>
                    </div>

                    <div class="w-16 h-16 rounded-2xl bg-neon-gradient text-white flex items-center justify-center text-3xl shadow-xl bg-neon-glow">
                        <i class="ri-wallet-3-fill"></i>
                    </div>
                </div>

                <!-- Card 2: Total Historical Earned -->
                <div class="glass-card p-8 rounded-2xl border border-white/10 relative overflow-hidden flex items-center justify-between">
                    <div class="space-y-2">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Total Lifetime Earnings</span>
                        <div class="text-4xl font-black text-white">
                            {{ number_format($wallet->total_earned, 2) }} <span class="text-sm font-bold text-green-400">EGP</span>
                        </div>
                        <p class="text-xs text-gray-400">Total net revenue accumulated from private sessions</p>
                    </div>

                    <div class="w-16 h-16 rounded-2xl bg-green-500/10 text-green-400 border border-green-500/30 flex items-center justify-center text-3xl shadow-xl">
                        <i class="ri-[#ff5b00] ri-funds-box-line"></i>
                    </div>
                </div>

            </div>

            <!-- Ledger Transactions History Table -->
            <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <h3 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                            <i class="ri-history-line neon-accent"></i> Ledger Transactions History
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">Audit trail of private session credits and 15% gym commission breakdown</p>
                    </div>
                    <span class="text-xs font-bold text-gray-400">Showing Recent 20 Movements</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-white/10 text-gray-400 uppercase font-black tracking-wider">
                                <th class="py-3 px-4">Transaction ID</th>
                                <th class="py-3 px-4">Type</th>
                                <th class="py-3 px-4">Gross Amount</th>
                                <th class="py-3 px-4">Gym Commission (15%)</th>
                                <th class="py-3 px-4">Net Credit (85%)</th>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 font-semibold text-gray-300">
                            @forelse($transactions as $txn)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="py-4 px-4 font-mono font-bold text-white">
                                        #TXN-{{ $txn->id }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider 
                                            {{ $txn->type === 'session_credit' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : 'bg-purple-500/20 text-purple-400 border border-purple-500/30' }}">
                                            {{ str_replace('_', ' ', $txn->type) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 font-bold text-white">
                                        {{ number_format($txn->amount, 2) }} EGP
                                    </td>
                                    <td class="py-4 px-4 text-red-400 font-bold">
                                        -{{ number_format($txn->commission_amount, 2) }} EGP
                                    </td>
                                    <td class="py-4 px-4 text-green-400 font-black text-sm">
                                        +{{ number_format($txn->net_amount, 2) }} EGP
                                    </td>
                                    <td class="py-4 px-4 text-gray-400">
                                        {{ $txn->created_at?->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-green-500/20 text-green-400 border border-green-500/30">
                                            {{ $txn->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-400 italic">
                                        No financial ledger transactions found for your trainer wallet yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
