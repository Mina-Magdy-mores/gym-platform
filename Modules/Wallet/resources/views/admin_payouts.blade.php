<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                Admin <span class="neon-accent">Trainer Payout Approvals</span>
            </h2>
            <div class="px-4 py-1 rounded-full bg-neon-gradient text-white text-xs font-black uppercase tracking-widest bg-neon-glow">
                Financial Settlement Control Panel
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(session('status') === 'payout_approved')
                <div class="p-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 text-sm font-bold flex items-center gap-2">
                    <i class="ri-checkbox-circle-fill text-lg"></i>
                    Payout request approved and settled successfully! Ledger transaction recorded.
                </div>
            @endif

            @if(session('status') === 'payout_rejected')
                <div class="p-4 rounded-xl bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 text-sm font-bold flex items-center gap-2">
                    <i class="ri-information-fill text-lg"></i>
                    Payout request rejected. Frozen balance restored back to trainer wallet.
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-bold flex items-center gap-2">
                    <i class="ri-error-warning-fill text-lg"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Admin Payout Requests Management Table -->
            <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <h3 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                            <i class="ri-bank-card-line neon-accent"></i> All Trainer Payout Requests
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">Review pending requests, verify InstaPay / Vodafone Cash details, and authorize transfers</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-white/10 text-gray-400 uppercase font-black tracking-wider">
                                <th class="py-3 px-4">Request ID</th>
                                <th class="py-3 px-4">Trainer Name</th>
                                <th class="py-3 px-4">Amount</th>
                                <th class="py-3 px-4">Payment Method</th>
                                <th class="py-3 px-4">Account / IPA Details</th>
                                <th class="py-3 px-4">Date Submitted</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 font-semibold text-gray-300">
                            @forelse($payoutRequests as $payout)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="py-4 px-4 font-mono font-bold text-white">#REQ-{{ $payout->id }}</td>
                                    <td class="py-4 px-4 font-bold text-white text-sm">{{ $payout->user->name ?? 'Trainer' }}</td>
                                    <td class="py-4 px-4 font-black text-green-400 text-sm">{{ number_format($payout->amount, 2) }} EGP</td>
                                    <td class="py-4 px-4 uppercase font-bold text-neon-accent">{{ str_replace('_', ' ', $payout->payment_method) }}</td>
                                    <td class="py-4 px-4 font-mono text-gray-200 bg-white/5 px-2 py-1 rounded">{{ $payout->account_details }}</td>
                                    <td class="py-4 px-4 text-gray-400">{{ $payout->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="py-4 px-4">
                                        @if($payout->status === 'pending')
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Pending</span>
                                        @elseif($payout->status === 'approved')
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-green-500/20 text-green-400 border border-green-500/30">Approved</span>
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
                                                        class="px-4 py-1.5 rounded-lg bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-green-500/30 font-black uppercase text-[10px] transition">
                                                        <i class="ri-check-line"></i> Approve
                                                    </button>
                                                </form>

                                                <!-- Reject Form -->
                                                <form action="{{ route('admin.payouts.reject', $payout->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Confirm rejecting this payout request? Frozen funds will be restored to trainer wallet.')"
                                                        class="px-4 py-1.5 rounded-lg bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500/30 font-black uppercase text-[10px] transition">
                                                        <i class="ri-close-line"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="flex items-center justify-end gap-2">
                                                @if($payout->status === 'approved')
                                                    <a href="{{ route('payouts.voucher', $payout->id) }}" target="_blank" class="px-2 py-1 rounded-lg bg-white/10 text-white hover:bg-white/20 transition text-[10px] font-bold inline-flex items-center gap-1">
                                                        <i class="ri-eye-line"></i> View
                                                    </a>
                                                    <a href="{{ route('payouts.download', $payout->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 hover:bg-[#ff5b00] hover:text-white transition text-[10px] font-bold inline-flex items-center gap-1">
                                                        <i class="ri-download-2-line"></i> Voucher
                                                    </a>
                                                @else
                                                    <span class="text-gray-500 text-xs italic">Processed</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-gray-400 italic">
                                        No trainer payout requests submitted yet.
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
