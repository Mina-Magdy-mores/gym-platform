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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Card 1: Available Balance -->
                <div class="glass-card p-8 rounded-2xl border border-white/10 relative overflow-hidden flex items-center justify-between">
                    <div class="space-y-2">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Available Balance</span>
                        <div class="text-3xl font-black text-white">
                            {{ number_format($wallet->balance, 2) }} <span class="text-sm font-bold neon-accent">EGP</span>
                        </div>
                        <p class="text-xs text-gray-400">Net earnings available for payout withdrawal</p>
                    </div>

                    <div class="w-14 h-14 rounded-2xl bg-neon-gradient text-white flex items-center justify-center text-2xl shadow-xl bg-neon-glow">
                        <i class="ri-wallet-3-fill"></i>
                    </div>
                </div>

                <!-- Card 2: Pending Payout Frozen Balance -->
                <div class="glass-card p-8 rounded-2xl border border-yellow-500/20 relative overflow-hidden flex items-center justify-between">
                    <div class="space-y-2">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Pending Payout (Frozen)</span>
                        <div class="text-3xl font-black text-yellow-400">
                            {{ number_format($wallet->pending_payout ?? 0.00, 2) }} <span class="text-sm font-bold text-yellow-400">EGP</span>
                        </div>
                        <p class="text-xs text-gray-400">Requested amount awaiting admin approval</p>
                    </div>

                    <div class="w-14 h-14 rounded-2xl bg-yellow-500/10 text-yellow-400 border border-yellow-500/30 flex items-center justify-center text-2xl shadow-xl">
                        <i class="ri-time-line"></i>
                    </div>
                </div>

                <!-- Card 3: Total Lifetime Earnings -->
                <div class="glass-card p-8 rounded-2xl border border-white/10 relative overflow-hidden flex items-center justify-between">
                    <div class="space-y-2">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Total Lifetime Earnings</span>
                        <div class="text-3xl font-black text-white">
                            {{ number_format($wallet->total_earned, 2) }} <span class="text-sm font-bold text-green-400">EGP</span>
                        </div>
                        <p class="text-xs text-gray-400">Total net revenue accumulated from private sessions</p>
                    </div>

                    <div class="w-14 h-14 rounded-2xl bg-green-500/10 text-green-400 border border-green-500/30 flex items-center justify-center text-2xl shadow-xl">
                        <i class="ri-funds-box-line"></i>
                    </div>
                </div>

            </div>

            <!-- Request Payout Form Card -->
            <div class="glass-card p-8 rounded-2xl border border-white/10 space-y-6">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <h3 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                            <i class="ri-bank-card-line neon-accent"></i> Request Payout Withdrawal
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">Withdraw your available wallet earnings to InstaPay or Vodafone Cash</p>
                    </div>
                </div>

                @if(session('status') === 'payout_requested')
                    <div class="p-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 text-sm font-bold flex items-center gap-2">
                        <i class="ri-checkbox-circle-fill text-lg"></i>
                        Payout request submitted successfully! Amount has been frozen pending admin review.
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-bold flex items-center gap-2">
                        <i class="ri-error-warning-fill text-lg"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-bold space-y-1">
                        @foreach ($errors->all() as $error)
                            <p class="flex items-center gap-2"><i class="ri-error-warning-fill"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('wallet.payout.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-2">Withdrawal Amount (EGP)</label>
                        <input type="number" step="0.01" min="1" name="amount" required placeholder="e.g. 500.00"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-neon-glow">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-2">Payment Method</label>
                        <select name="payment_method" required style="background-color: #12141a; color: #ffffff;" class="w-full border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-neon-glow">
                            <option value="instapay" style="background-color: #12141a; color: #ffffff;">InstaPay Handle / Phone</option>
                            <option value="vodafone_cash" style="background-color: #12141a; color: #ffffff;">Vodafone Cash Number</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-2">Account Details (IPA / Number)</label>
                        <input type="text" name="account_details" required placeholder="e.g. name@instapay or 01011112222"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-neon-glow">
                    </div>

                    <div class="md:col-span-3 flex justify-end">
                        <button type="submit" @if($wallet->balance <= 0) disabled @endif
                            class="px-8 py-3 rounded-xl bg-neon-gradient text-white font-black text-sm uppercase tracking-wider shadow-lg bg-neon-glow hover:opacity-90 transition disabled:opacity-50">
                            @if($wallet->balance <= 0) Insufficient Balance @else Submit Payout Request @endif
                        </button>
                    </div>
                </form>
            </div>

            <!-- Payout Requests Audit Trail Table -->
            @if(isset($payoutRequests) && count($payoutRequests) > 0)
                <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4">
                        <div>
                            <h3 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                                <i class="ri-exchange-dollar-line neon-accent"></i> Payout Requests History
                            </h3>
                            <p class="text-xs text-gray-400 mt-1">Status of your pending and completed payout withdrawal requests</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-white/10 text-gray-400 uppercase font-black tracking-wider">
                                    <th class="py-3 px-4">Request ID</th>
                                    <th class="py-3 px-4">Amount</th>
                                    <th class="py-3 px-4">Method</th>
                                    <th class="py-3 px-4">Account Details</th>
                                    <th class="py-3 px-4">Date</th>
                                    <th class="py-3 px-4 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 font-semibold text-gray-300">
                                @foreach($payoutRequests as $payout)
                                    <tr class="hover:bg-white/5 transition">
                                        <td class="py-4 px-4 font-mono font-bold text-white">#REQ-{{ $payout->id }}</td>
                                        <td class="py-4 px-4 font-black text-white text-sm">{{ number_format($payout->amount, 2) }} EGP</td>
                                        <td class="py-4 px-4 uppercase font-bold text-neon-accent">{{ str_replace('_', ' ', $payout->payment_method) }}</td>
                                        <td class="py-4 px-4 font-mono text-gray-300">{{ $payout->account_details }}</td>
                                        <td class="py-4 px-4 text-gray-400">{{ $payout->created_at?->format('Y-m-d H:i') }}</td>
                                        <td class="py-4 px-4 text-right flex items-center justify-end gap-2">
                                            @if($payout->status === 'approved')
                                                <a href="{{ route('payouts.voucher', $payout->id) }}" target="_blank" class="px-2 py-1 rounded-lg bg-white/10 text-white hover:bg-white/20 transition text-[10px] font-bold inline-flex items-center gap-1">
                                                    <i class="ri-eye-line"></i> View
                                                </a>
                                                <a href="{{ route('payouts.download', $payout->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 hover:bg-[#ff5b00] hover:text-white transition text-[10px] font-bold inline-flex items-center gap-1">
                                                    <i class="ri-download-2-line"></i> Voucher
                                                </a>
                                            @endif

                                            @if($payout->status === 'pending')
                                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Pending Admin Review</span>
                                            @elseif($payout->status === 'approved')
                                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-green-500/20 text-green-400 border border-green-500/30">Approved & Transferred</span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30">Rejected</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

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
                                    <td class="py-4 px-4 text-right flex items-center justify-end gap-2">
                                        @if($txn->booking && $txn->booking->payment)
                                            <a href="{{ route('invoices.show', $txn->booking->payment->id) }}" target="_blank" class="px-2 py-1 rounded-lg bg-white/10 text-white hover:bg-white/20 transition text-[10px] font-bold inline-flex items-center gap-1">
                                                <i class="ri-eye-line"></i> View
                                            </a>
                                            <a href="{{ route('invoices.download', $txn->booking->payment->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 hover:bg-[#ff5b00] hover:text-white transition text-[10px] font-bold inline-flex items-center gap-1">
                                                <i class="ri-download-2-line"></i> PDF
                                            </a>
                                        @elseif($txn->type === 'payout')
                                            @php
                                                $matchingPayout = isset($payoutRequests) ? $payoutRequests->firstWhere('amount', $txn->amount) : null;
                                            @endphp
                                            @if($matchingPayout)
                                                <a href="{{ route('payouts.voucher', $matchingPayout->id) }}" target="_blank" class="px-2 py-1 rounded-lg bg-white/10 text-white hover:bg-white/20 transition text-[10px] font-bold inline-flex items-center gap-1">
                                                    <i class="ri-eye-line"></i> View
                                                </a>
                                                <a href="{{ route('payouts.download', $matchingPayout->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 hover:bg-[#ff5b00] hover:text-white transition text-[10px] font-bold inline-flex items-center gap-1">
                                                    <i class="ri-download-2-line"></i> Voucher
                                                </a>
                                            @endif
                                        @elseif($txn->type === 'session_credit')
                                            <span class="px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[10px] font-bold inline-flex items-center gap-1">
                                                <i class="ri-vip-crown-2-line"></i> Covered by Plan
                                            </span>
                                        @endif
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
