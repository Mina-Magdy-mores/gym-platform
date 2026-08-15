<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                @if(Auth::user()->hasRole('admin'))
                    <span class="text-[#ff5b00]">Admin</span> Control Panel
                @elseif(Auth::user()->hasRole('trainer'))
                    <span class="neon-accent">Coach</span> Dashboard
                @else
                    <span class="neon-accent">Member</span> Dashboard
                @endif
            </h2>
            <div class="px-4 py-1 rounded-full bg-neon-gradient text-white text-xs font-black uppercase tracking-widest bg-neon-glow">
                {{ Auth::user()->roles->first()?->name ?? 'Member' }}
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(Auth::user()->hasRole('admin'))
                <!-- ========================================== -->
                <!-- MASTER ADMIN COMMAND CENTER VIEW           -->
                <!-- ========================================== -->

                <!-- Admin Welcome & Executive Control Banner -->
                <div class="p-8 glass-card rounded-2xl border border-red-500/20 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6 shadow-2xl">
                    <div class="space-y-3 text-center md:text-left">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-500/20 border border-red-500/40 text-xs font-black text-red-400 uppercase tracking-wider">
                            <i class="ri-shield-keyhole-line"></i> Master Admin Authority & Telemetry
                        </div>
                        <h3 class="text-3xl font-black uppercase tracking-wide text-white">
                            Welcome, Master Admin <span class="text-[#ff5b00]">{{ $user->name }}</span>
                        </h3>
                        <p class="text-gray-400 text-sm max-w-2xl leading-relaxed">
                            Live operational command center: monitor platform gross revenue, active memberships, certified coaches, session bookings, and financial settlement approvals.
                        </p>
                        <div class="pt-2 flex flex-wrap items-center gap-2.5">
                            <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#ff5b00] hover:bg-[#ff5b00]/90 text-white font-black text-xs uppercase tracking-wider transition shadow-lg">
                                <i class="ri-calendar-check-line"></i> Bookings Panel
                            </a>
                            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition border border-white/10">
                                <i class="ri-money-dollar-circle-line text-emerald-400"></i> Master Ledger
                            </a>
                            <a href="{{ route('admin.payouts.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition border border-white/10">
                                <i class="ri-bank-card-line text-indigo-400"></i> Trainer Payouts
                                @if(($adminStats['pendingPayoutsCount'] ?? 0) > 0)
                                    <span class="px-1.5 py-0.5 rounded-full bg-[#ff5b00] text-white text-[9px] font-black animate-pulse">
                                        {{ $adminStats['pendingPayoutsCount'] }}
                                    </span>
                                @endif
                            </a>
                            <a href="{{ route('trainer.members.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition border border-white/10">
                                <i class="ri-user-heart-line text-[#ff5b00]"></i> Athletes Roster
                            </a>
                            <a href="{{ route('admin.rules.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition border border-white/10">
                                <i class="ri-file-shield-line text-amber-400"></i> Manage Rules
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition border border-white/10">
                                <i class="ri-shield-user-line text-red-400"></i> Users & Security
                            </a>
                        </div>
                    </div>

                    <!-- Admin Avatar Preview Badge -->
                    <div class="shrink-0 text-center">
                        <div class="relative w-28 h-28 rounded-2xl overflow-hidden glass-card border border-red-500/30 shadow-2xl flex items-center justify-center mx-auto">
                            @if($user->getFirstMediaUrl('avatar'))
                                <img src="{{ $user->getFirstMediaUrl('avatar') }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-red-600 to-[#ff5b00] flex items-center justify-center text-white text-3xl font-black uppercase">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <span class="inline-block mt-2 text-[10px] font-mono text-gray-400 uppercase tracking-widest">
                            Root Security Level
                        </span>
                    </div>
                </div>

                <!-- Admin Key Telemetry Stats Grid (5 Cards) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Total Revenue -->
                    <div class="p-5 rounded-2xl bg-[#12141c]/90 border border-emerald-500/20 space-y-2 hover:border-emerald-500/40 transition">
                        <div class="flex items-center justify-between text-gray-400">
                            <span class="text-xs font-bold uppercase tracking-wider">Gross Inflows</span>
                            <i class="ri-money-dollar-circle-line text-lg text-emerald-400"></i>
                        </div>
                        <div class="text-xl font-black text-white font-mono">
                            {{ number_format($adminStats['totalRevenue'] ?? 0, 2) }} <span class="text-xs text-emerald-400 font-sans">EGP</span>
                        </div>
                        <div class="text-[10px] text-gray-400">Total settled platform volume</div>
                    </div>

                    <!-- Active Members -->
                    <div class="p-5 rounded-2xl bg-[#12141c]/90 border border-white/10 space-y-2 hover:border-[#ff5b00]/40 transition">
                        <div class="flex items-center justify-between text-gray-400">
                            <span class="text-xs font-bold uppercase tracking-wider">Active Members</span>
                            <i class="ri-user-smile-line text-lg text-[#ff5b00]"></i>
                        </div>
                        <div class="text-xl font-black text-white font-mono">
                            {{ $adminStats['activeMembers'] ?? 0 }} <span class="text-xs text-gray-400 font-sans">/ {{ $adminStats['totalMembers'] ?? 0 }} Total</span>
                        </div>
                        <div class="text-[10px] text-emerald-400 font-bold">Paid subscription holders</div>
                    </div>

                    <!-- Certified Coaches -->
                    <div class="p-5 rounded-2xl bg-[#12141c]/90 border border-white/10 space-y-2 hover:border-sky-500/40 transition">
                        <div class="flex items-center justify-between text-gray-400">
                            <span class="text-xs font-bold uppercase tracking-wider">Trainers Roster</span>
                            <i class="ri-user-heart-line text-lg text-sky-400"></i>
                        </div>
                        <div class="text-xl font-black text-white font-mono">
                            {{ $adminStats['totalTrainers'] ?? 0 }} <span class="text-xs text-sky-400 font-sans">Coaches</span>
                        </div>
                        <div class="text-[10px] text-gray-400">Active certified trainers</div>
                    </div>

                    <!-- Today's Sessions -->
                    <div class="p-5 rounded-2xl bg-[#12141c]/90 border border-white/10 space-y-2 hover:border-purple-500/40 transition">
                        <div class="flex items-center justify-between text-gray-400">
                            <span class="text-xs font-bold uppercase tracking-wider">Today's Sessions</span>
                            <i class="ri-calendar-check-line text-lg text-purple-400"></i>
                        </div>
                        <div class="text-xl font-black text-white font-mono">
                            {{ $adminStats['todayBookings'] ?? 0 }} <span class="text-xs text-purple-400 font-sans">Booked</span>
                        </div>
                        <div class="text-[10px] text-gray-400">{{ date('M d, Y') }}</div>
                    </div>

                    <!-- Pending Payouts -->
                    <a href="{{ route('admin.payouts.index') }}" class="p-5 rounded-2xl bg-[#12141c]/90 border border-amber-500/20 space-y-2 hover:border-amber-500/50 transition group block">
                        <div class="flex items-center justify-between text-gray-400">
                            <span class="text-xs font-bold uppercase tracking-wider group-hover:text-amber-300">Pending Payouts</span>
                            <i class="ri-bank-card-line text-lg text-amber-400"></i>
                        </div>
                        <div class="text-xl font-black text-white font-mono">
                            {{ $adminStats['pendingPayoutsCount'] ?? 0 }} <span class="text-xs text-amber-400 font-sans">Requests</span>
                        </div>
                        <div class="text-[10px] text-amber-400 font-bold">
                            {{ number_format($adminStats['pendingPayoutsAmount'] ?? 0, 2) }} EGP Pending
                        </div>
                    </a>
                </div>

                <!-- Admin Dual Master Tables (Recent Bookings & Live Revenue Inflows) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Left: Recent Platform PT Bookings (7 cols) -->
                    <div class="lg:col-span-7 glass-card p-6 rounded-2xl border border-white/10 space-y-4 shadow-xl">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                                <i class="ri-calendar-event-line text-[#ff5b00]"></i> Recent Session Bookings Ledger
                            </h3>
                            <a href="{{ route('admin.bookings.index') }}" class="text-[11px] font-bold text-[#ff5b00] hover:text-white transition">
                                Master Bookings &rarr;
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="text-gray-400 border-b border-white/5 font-black uppercase text-[10px]">
                                        <th class="pb-2">ID</th>
                                        <th class="pb-2">Member</th>
                                        <th class="pb-2">Trainer</th>
                                        <th class="pb-2">Date & Time</th>
                                        <th class="pb-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5 text-gray-300">
                                    @forelse($adminRecentBookings as $bk)
                                        <tr class="hover:bg-white/5 transition">
                                            <td class="py-3 font-mono font-bold text-white">#{{ $bk->id }}</td>
                                            <td class="py-3">
                                                <div class="font-bold text-white">{{ $bk->user->name ?? 'N/A' }}</div>
                                                <div class="text-[10px] text-gray-400">{{ $bk->user->activeSubscription->plan->name ?? 'No Plan' }}</div>
                                            </td>
                                            <td class="py-3">
                                                <div class="font-bold text-white">{{ $bk->trainer->name ?? 'Unassigned' }}</div>
                                            </td>
                                            <td class="py-3 font-mono text-[11px]">
                                                <div>{{ $bk->booking_date }}</div>
                                                <div class="text-gray-400 text-[10px]">{{ $bk->start_time }}</div>
                                            </td>
                                            <td class="py-3">
                                                @if($bk->status === 'confirmed')
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-green-500/20 text-green-400 border border-green-500/30">Confirmed</span>
                                                @elseif($bk->status === 'completed')
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-blue-500/20 text-blue-400 border border-blue-500/30">Completed</span>
                                                @elseif($bk->status === 'cancelled')
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-red-500/20 text-red-400 border border-red-500/30">Cancelled</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-gray-500/20 text-gray-400">{{ $bk->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-gray-400 italic">No bookings recorded yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Right: Live Revenue Inflows (5 cols) -->
                    <div class="lg:col-span-5 glass-card p-6 rounded-2xl border border-white/10 space-y-4 shadow-xl">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                                <i class="ri-money-dollar-circle-line text-emerald-400"></i> Live Revenue Inflows
                            </h3>
                            <a href="{{ route('admin.payments.index') }}" class="text-[11px] font-bold text-emerald-400 hover:text-white transition">
                                Master Ledger &rarr;
                            </a>
                        </div>

                        <div class="space-y-3">
                            @forelse($adminRecentPayments as $pmt)
                                <div class="p-3 rounded-xl bg-white/[0.03] border border-white/5 flex items-center justify-between gap-3 hover:border-white/10 transition">
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-white text-xs truncate">{{ $pmt->user->name ?? 'Member' }}</div>
                                        <div class="text-[10px] text-gray-400 truncate">{{ $pmt->plan->name ?? ($pmt->payment_method ?? 'Payment') }}</div>
                                        <div class="text-[9px] font-mono text-gray-500">{{ $pmt->created_at->diffForHumans() }}</div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <div class="font-mono font-black text-sm text-green-400">+{{ number_format($pmt->amount, 2) }} EGP</div>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-white/5 border border-white/10 text-gray-300">{{ $pmt->payment_method ?? 'Paymob' }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="py-6 text-center text-gray-400 text-xs italic">No settled transactions yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Agreed Gym Terms & Regulations Section for Admin -->
                <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-white/10 pb-4">
                        <div>
                            <h3 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                                <i class="ri-file-shield-line neon-accent"></i> Agreed Gym Terms & Regulations
                            </h3>
                            <p class="text-xs text-gray-400 mt-1">Official membership rules agreed upon at subscription checkout</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.rules.index') }}" class="px-4 py-1.5 rounded-xl bg-[#ff5b00]/20 hover:bg-[#ff5b00] text-[#ff5b00] hover:text-white border border-[#ff5b00]/40 text-xs font-black uppercase tracking-wider transition flex items-center gap-1.5 cursor-pointer">
                                <i class="ri-settings-4-line"></i> Manage / Edit Rules CRUD
                            </a>
                            <span class="px-3.5 py-1.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
                                <i class="ri-shield-check-fill"></i> Agreed & Accepted
                            </span>
                        </div>
                    </div>

                    <!-- 2-Column Rules Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-300">
                        @foreach($gymRules as $rule)
                            <div class="p-3.5 rounded-xl bg-white/5 border border-white/5 flex items-start gap-3 hover:border-white/10 transition">
                                <span class="px-2 py-0.5 rounded bg-neon-gradient text-white font-black text-[10px] shrink-0 mt-0.5">
                                    #{{ $rule->rule_number }}
                                </span>
                                <p class="leading-relaxed">{{ $rule->rule_text }}</p>
                            </div>
                        @endforeach
                    </div>

                    <!-- Policy Compliance Warning Note Banner -->
                    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-between text-xs text-amber-300 mt-4">
                        <div class="flex items-center gap-3">
                            <i class="ri-shield-user-line text-xl text-amber-400 shrink-0"></i>
                            <span><strong>Important Policy Notice:</strong> All facility rules and conduct policies must be strictly followed. Violation of facility terms may lead to administrative review or temporary membership suspension.</span>
                        </div>
                    </div>
                </div>

            @elseif(Auth::user()->hasRole('trainer'))
                <!-- ========================================== -->
                <!-- TRAINER / COACH DASHBOARD VIEW             -->
                <!-- ========================================== -->

                <!-- Coach Welcome Header Card -->
                <div class="p-8 glass-card rounded-2xl border border-white/5 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="space-y-3 text-center md:text-left">
                        <div class="inline-block px-3 py-1 rounded-full bg-[#ff5b00]/20 border border-[#ff5b00]/40 text-xs font-bold text-[#ff5b00]">
                            Certified Personal Trainer Portal
                        </div>
                        @php
                            $cleanCaptainName = preg_replace('/^(Captain|Coach)\s+/i', '', $user->name);
                        @endphp
                        <h3 class="text-3xl font-black uppercase tracking-wide">
                            Welcome Back, Captain <span class="neon-accent">{{ $cleanCaptainName }}</span>
                        </h3>
                        <p class="text-gray-400 text-sm max-w-lg leading-relaxed">
                            Track your assigned athletes, manage 1-on-1 PT training sessions, and review your wallet commission earnings.
                        </p>
                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            <a href="{{ route('trainer.members.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-neon-gradient text-white font-bold text-xs bg-neon-glow hover:opacity-90 transition">
                                <span>Athletes Roster</span> <i class="ri-user-heart-line"></i>
                            </a>
                            <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/10">
                                <span>PT Sessions</span> <i class="ri-calendar-check-line"></i>
                            </a>
                            <a href="{{ route('wallet.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/10">
                                <span>My Wallet</span> <i class="ri-wallet-3-line"></i>
                            </a>
                            <a href="{{ route('chat.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/10">
                                <span>Real-Time Chat</span> <i class="ri-chat-smile-2-line"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Coach Avatar Preview Badge -->
                    <div class="shrink-0">
                        <div class="relative w-32 h-32 rounded-2xl overflow-hidden glass-card border border-[#ff5b00]/30 shadow-2xl flex items-center justify-center">
                            @if($user->getFirstMediaUrl('avatar'))
                                <img src="{{ $user->getFirstMediaUrl('avatar') }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <div class="text-4xl font-black text-white neon-accent">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Coach KPI Stat Cards Grid (4 Glass Cards) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Stat Card 1: Assigned Athletes -->
                    <a href="{{ route('trainer.members.index') }}" class="glass-card p-6 rounded-2xl border border-white/5 space-y-3 hover:border-[#ff5b00]/40 transition group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Assigned Athletes</span>
                            <div class="w-10 h-10 rounded-xl bg-[#ff5b00]/20 border border-[#ff5b00]/30 text-[#ff5b00] flex items-center justify-center text-xl group-hover:scale-110 transition">
                                <i class="ri-user-heart-line"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-white">
                            {{ $trainerStats['totalAthletes'] ?? 0 }}
                        </div>
                        <div class="text-[10px] text-gray-400 font-mono flex items-center gap-1">
                            <span>Athletes under your coaching</span>
                        </div>
                    </a>

                    <!-- Stat Card 2: Today's PT Sessions -->
                    <a href="{{ route('bookings.index') }}" class="glass-card p-6 rounded-2xl border border-white/5 space-y-3 hover:border-emerald-500/40 transition group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Today's Sessions</span>
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl group-hover:scale-110 transition">
                                <i class="ri-calendar-event-line"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-white">
                            {{ $trainerStats['todaySessions'] ?? 0 }}
                        </div>
                        <div class="text-[10px] text-gray-400 font-mono flex items-center gap-1">
                            <span>Confirmed sessions today</span>
                        </div>
                    </a>

                    <!-- Stat Card 3: Total Bookings History -->
                    <a href="{{ route('bookings.index') }}" class="glass-card p-6 rounded-2xl border border-white/5 space-y-3 hover:border-blue-500/40 transition group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total Bookings</span>
                            <div class="w-10 h-10 rounded-xl bg-blue-500/20 border border-blue-500/30 text-blue-400 flex items-center justify-center text-xl group-hover:scale-110 transition">
                                <i class="ri-shield-star-line"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-white">
                            {{ $trainerStats['totalBookings'] ?? 0 }}
                        </div>
                        <div class="text-[10px] text-gray-400 font-mono flex items-center gap-1">
                            <span>Total 1-on-1 sessions booked</span>
                        </div>
                    </a>

                    <!-- Stat Card 4: Wallet Payout Balance -->
                    <a href="{{ route('wallet.index') }}" class="glass-card p-6 rounded-2xl border border-white/5 space-y-3 hover:border-amber-500/40 transition group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Wallet Balance</span>
                            <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/30 text-amber-400 flex items-center justify-center text-xl group-hover:scale-110 transition">
                                <i class="ri-wallet-3-line"></i>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-white">
                            EGP {{ number_format($trainerStats['walletBalance'] ?? 0, 2) }}
                        </div>
                        <div class="text-[10px] text-gray-400 font-mono flex items-center gap-1">
                            <span>Net available payout balance</span>
                        </div>
                    </a>
                </div>

                <!-- Coach Upcoming Appointments Table -->
                <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-white/10 pb-4">
                        <div>
                            <h3 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                                <i class="ri-calendar-check-line neon-accent"></i> Upcoming 1-on-1 PT Appointments
                            </h3>
                            <p class="text-xs text-gray-400 mt-1">Sessions booked by your athletes</p>
                        </div>
                        <a href="{{ route('bookings.index') }}" class="text-xs font-bold text-[#ff5b00] hover:text-white transition">View All Sessions &rarr;</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-white/10 text-gray-400 text-xs uppercase tracking-wider font-bold">
                                    <th class="py-3 px-4">Athlete / Member</th>
                                    <th class="py-3 px-4">Booking Date</th>
                                    <th class="py-3 px-4">Time Slot</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Quick Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-sm font-semibold">
                                @forelse($upcomingBookings->take(5) as $bk)
                                    @php
                                        $athlete = $bk->user;
                                        $cleanAthleteName = preg_replace('/[^\p{L}\p{N}\s]/u', '', $athlete?->name ?? 'Athlete');
                                    @endphp
                                    <tr class="hover:bg-white/5 transition">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-neon-gradient flex items-center justify-center font-black text-white text-sm uppercase shadow-md">
                                                    {{ strtoupper(mb_substr($cleanAthleteName, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-white">{{ $cleanAthleteName }}</div>
                                                    <div class="text-[10px] text-gray-400 font-mono">{{ $athlete?->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 font-mono text-gray-300 text-xs">
                                            {{ $bk->booking_date }}
                                        </td>
                                        <td class="py-4 px-4 font-mono text-xs text-[#ff5b00]">
                                            {{ $bk->start_time }} - {{ $bk->end_time }}
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($bk->status === 'confirmed')
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Confirmed</span>
                                            @elseif($bk->status === 'completed')
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-blue-500/20 text-blue-400 border border-blue-500/30">Completed</span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-gray-500/20 text-gray-400 border border-gray-500/30">{{ ucfirst($bk->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <a href="{{ route('chat.start', $bk->user_id) }}" class="px-3 py-1.5 rounded-lg bg-neon-gradient text-white text-xs font-black uppercase tracking-wider inline-flex items-center gap-1 shadow-md shadow-[#ff5b00]/20 hover:opacity-90 transition">
                                                <i class="ri-chat-smile-2-line"></i> Chat Athlete
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-400 italic">
                                            No upcoming PT sessions booked yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Trainer Section: Assigned Athletes Quick Roster -->
                <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-white/10 pb-4">
                        <div>
                            <h3 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                                <i class="ri-user-heart-line text-[#ff5b00]"></i> Assigned Athletes Roster
                            </h3>
                            <p class="text-xs text-gray-400 mt-1">Quick access to manage workout routines & diet plans</p>
                        </div>
                        <a href="{{ route('trainer.members.index') }}" class="text-xs font-bold text-[#ff5b00] hover:text-white transition">View All Roster &rarr;</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @forelse($assignedAthletes->take(3) as $ath)
                            @php
                                $cleanAthName = preg_replace('/[^\p{L}\p{N}\s]/u', '', $ath->name);
                            @endphp
                            <div class="p-4 rounded-xl bg-white/5 border border-white/10 flex flex-col justify-between gap-3 hover:border-[#ff5b00]/40 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-neon-gradient flex items-center justify-center font-black text-white text-sm uppercase shrink-0">
                                        {{ strtoupper(mb_substr($cleanAthName, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-white text-sm truncate">{{ $ath->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono truncate">{{ $ath->email }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 pt-2 border-t border-white/10">
                                    <a href="{{ route('trainer.workout.create', $ath->id) }}" class="flex-1 py-1.5 px-2 rounded-lg bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 hover:bg-[#ff5b00] hover:text-white transition font-bold text-[10px] text-center">
                                        + Workout
                                    </a>
                                    <a href="{{ route('trainer.diet.create', $ath->id) }}" class="flex-1 py-1.5 px-2 rounded-lg bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-green-500 hover:text-white transition font-bold text-[10px] text-center">
                                        + Diet
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-6 text-center text-gray-400 text-xs italic">
                                No assigned athletes yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Agreed Gym Terms & Regulations Section for Trainers -->
                <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-white/10 pb-4">
                        <div>
                            <h3 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                                <i class="ri-file-shield-line neon-accent"></i> Official Gym Policy & Conduct Regulations
                            </h3>
                            <p class="text-xs text-gray-400 mt-1">Platform and facility operational rules</p>
                        </div>

                        <span class="px-3.5 py-1.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
                            <i class="ri-shield-check-fill"></i> Active Regulations
                        </span>
                    </div>

                    <!-- 2-Column Rules Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-300">
                        @foreach($gymRules as $rule)
                            <div class="p-3.5 rounded-xl bg-white/5 border border-white/5 flex items-start gap-3 hover:border-white/10 transition">
                                <span class="px-2 py-0.5 rounded bg-neon-gradient text-white font-black text-[10px] shrink-0 mt-0.5">
                                    #{{ $rule->rule_number }}
                                </span>
                                <p class="leading-relaxed">{{ $rule->rule_text }}</p>
                            </div>
                        @endforeach
                    </div>

                    <!-- Policy Compliance Warning Note Banner -->
                    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-between text-xs text-amber-300 mt-4">
                        <div class="flex items-center gap-3">
                            <i class="ri-shield-user-line text-xl text-amber-400 shrink-0"></i>
                            <span><strong>Important Policy Notice:</strong> All facility rules and conduct policies must be strictly followed. Violation of facility terms may lead to administrative review or temporary membership suspension.</span>
                        </div>
                    </div>
                </div>

            @else
                <!-- ========================================== -->
                <!-- MEMBER DASHBOARD VIEW                      -->
                <!-- ========================================== -->

                <!-- Welcome Header Card -->
                <div class="p-8 glass-card rounded-2xl border border-white/5 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="space-y-3 text-center md:text-left">
                        <div class="inline-block px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-gray-300">
                            Welcome Back, Champion!
                        </div>
                        <h3 class="text-3xl font-black uppercase tracking-wide">
                            Hello, <span class="neon-accent">{{ $user->name }}</span>
                        </h3>
                        <p class="text-gray-400 text-sm max-w-lg leading-relaxed">
                            Track your gym subscription benefits balance, view your remaining freeze days, and monitor your booked personal trainer sessions.
                        </p>
                        <div class="pt-2 flex flex-wrap items-center gap-4">
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-sm transition border border-white/10">
                                <span>Manage Profile</span> <i class="ri-user-settings-line"></i>
                            </a>
                            <a href="{{ url('/#plans') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-neon-gradient text-white font-bold text-sm bg-neon-glow hover:opacity-90 transition">
                                <span>Explore Plans</span> <i class="ri-fire-fill"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Avatar Preview Badge -->
                    <div class="shrink-0">
                        <div class="relative w-32 h-32 rounded-2xl overflow-hidden glass-card border border-[#ff5b00]/30 shadow-2xl flex items-center justify-center">
                            @if($user->getFirstMediaUrl('avatar'))
                                <img src="{{ $user->getFirstMediaUrl('avatar') }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <div class="text-4xl font-black text-white neon-accent">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Member Dashboard Outer Wrapper with Alpine Modals -->
                <div x-data="{ showWorkoutModal: false, showDietModal: false }">
                    <div class="space-y-8">
                        <!-- Active Subscription Header Banner -->
                        <div class="glass-card p-6 rounded-2xl border border-white/5 space-y-4">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-white/10 pb-4">
                                <div class="flex items-center gap-3">
                                    <span class="p-3 rounded-xl bg-neon-gradient text-white text-2xl shadow-lg">
                                        <i class="ri-vip-crown-fill"></i>
                                    </span>
                                    <div>
                                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Current Membership</span>
                                        <h4 class="text-xl font-black text-white uppercase tracking-wide">
                                            {{ $activeSub ? ($activeSub->plan?->name ?? 'Custom Membership Plan') : 'No Active Membership Plan' }}
                                        </h4>
                                    </div>
                                </div>

                                @if($activeSub)
                                    <div class="flex items-center gap-3">
                                        @if(isset($activeSub->payment) && $activeSub->payment)
                                            <a href="{{ route('invoices.show', $activeSub->payment->id) }}" target="_blank" class="px-4 py-1.5 rounded-full bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/40 hover:bg-[#ff5b00] hover:text-white transition text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
                                                <i class="ri-file-pdf-line"></i> Tax Receipt
                                            </a>
                                        @endif
                                        <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-400 border border-green-500/30 text-xs font-black uppercase tracking-wider">
                                            Active Until {{ $activeSub->ends_at->format('Y-m-d') }}
                                        </span>
                                        <span class="px-3 py-1 rounded-full bg-neon-gradient text-white text-xs font-black uppercase tracking-wider bg-neon-glow">
                                            {{ (int) max(0, ceil(now()->diffInDays($activeSub->ends_at))) }} Days Left
                                        </span>
                                    </div>
                                @else
                                    <a href="{{ url('/#plans') }}" class="px-5 py-2 rounded-full bg-neon-gradient text-white text-xs font-black uppercase tracking-wider bg-neon-glow hover:opacity-90 transition">
                                        Subscribe Now
                                    </a>
                                @endif
                            </div>

                            @if(!$activeSub)
                                <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-between text-xs text-amber-300">
                                    <div class="flex items-center gap-2">
                                        <i class="ri-alert-line text-lg"></i>
                                        <span>You do not have an active membership. Subscribe now to unlock full gym access and trainer sessions.</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Subscription Benefits Usage Balance Grid (7 Neon Glass Cards) -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-white uppercase flex items-center gap-2">
                                    <i class="ri-dashboard-3-line neon-accent"></i> Membership Benefits Balance
                                </h3>
                                <span class="text-xs text-gray-400">Real-time Benefits Meter (7 Active Perks)</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-4">
                                <!-- Card 1: Freeze Balance -->
                                <div class="glass-card p-5 rounded-2xl border border-white/5 space-y-3 relative overflow-hidden">
                                    <div class="flex items-center justify-between text-gray-400 text-[10px] font-bold uppercase">
                                        <span>Freeze Allowance</span>
                                        <i class="ri-snowflake-line text-base text-cyan-400"></i>
                                    </div>
                                    <div class="text-xl font-black text-white flex items-baseline gap-1">
                                        <span>{{ $activeSub ? $activeSub->remaining_freeze_days : 0 }}</span>
                                        <span class="text-xs font-bold text-gray-400">/ {{ $activeSub && $activeSub->plan ? $activeSub->plan->freeze_days : 0 }} Days</span>
                                    </div>
                                    <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-cyan-400 h-full rounded-full" style="width: {{ $activeSub && $activeSub->plan ? min(100, ($activeSub->remaining_freeze_days / max(1, $activeSub->plan->freeze_days)) * 100) : 0 }}%"></div>
                                    </div>
                                </div>

                                <!-- Card 2: PT Sessions Balance -->
                                <div class="glass-card p-5 rounded-2xl border border-white/5 space-y-3 relative overflow-hidden">
                                    <div class="flex items-center justify-between text-gray-400 text-[10px] font-bold uppercase">
                                        <span>PT Sessions</span>
                                        <i class="ri-user-star-line text-base text-[#ff5b00]"></i>
                                    </div>
                                    <div class="text-xl font-black text-white flex items-baseline gap-1">
                                        <span>{{ $activeSub ? $activeSub->remaining_pt_sessions : 0 }}</span>
                                        <span class="text-xs font-bold text-gray-400">/ {{ $activeSub && $activeSub->plan ? $activeSub->plan->pt_sessions : 0 }} Sessions</span>
                                    </div>
                                    <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-[#ff5b00] h-full rounded-full" style="width: {{ $activeSub && $activeSub->plan ? min(100, ($activeSub->remaining_pt_sessions / max(1, $activeSub->plan->pt_sessions)) * 100) : 0 }}%"></div>
                                    </div>
                                </div>

                                <!-- Card 3: InBody Balance -->
                                <div class="glass-card p-5 rounded-2xl border border-white/5 space-y-3 relative overflow-hidden">
                                    <div class="flex items-center justify-between text-gray-400 text-[10px] font-bold uppercase">
                                        <span>InBody Scans</span>
                                        <i class="ri-scales-3-line text-base text-emerald-400"></i>
                                    </div>
                                    <div class="text-xl font-black text-white flex items-baseline gap-1">
                                        <span>{{ $activeSub ? $activeSub->remaining_inbody_scans : 0 }}</span>
                                        <span class="text-xs font-bold text-gray-400">/ {{ $activeSub && $activeSub->plan ? $activeSub->plan->inbody_scans : 0 }} Scans</span>
                                    </div>
                                    <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-emerald-400 h-full rounded-full" style="width: {{ $activeSub && $activeSub->plan ? min(100, ($activeSub->remaining_inbody_scans / max(1, $activeSub->plan->inbody_scans)) * 100) : 0 }}%"></div>
                                    </div>
                                </div>

                                <!-- Card 4: Invitations Balance -->
                                <div class="glass-card p-5 rounded-2xl border border-white/5 space-y-3 relative overflow-hidden">
                                    <div class="flex items-center justify-between text-gray-400 text-[10px] font-bold uppercase">
                                        <span>Guest Passes</span>
                                        <i class="ri-user-add-line text-base text-purple-400"></i>
                                    </div>
                                    <div class="text-xl font-black text-white flex items-baseline gap-1">
                                        <span>{{ $activeSub ? $activeSub->remaining_invitations : 0 }}</span>
                                        <span class="text-xs font-bold text-gray-400">/ {{ $activeSub && $activeSub->plan ? $activeSub->plan->invitations_count : 0 }} Passes</span>
                                    </div>
                                    <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-purple-400 h-full rounded-full" style="width: {{ $activeSub && $activeSub->plan ? min(100, ($activeSub->remaining_invitations / max(1, $activeSub->plan->invitations_count)) * 100) : 0 }}%"></div>
                                    </div>
                                </div>

                                <!-- Card 5: Kickboxing Classes -->
                                <div class="glass-card p-5 rounded-2xl border border-white/5 space-y-3 relative overflow-hidden">
                                    <div class="flex items-center justify-between text-gray-400 text-[10px] font-bold uppercase">
                                        <span>Kickboxing</span>
                                        <i class="ri-boxing-line text-base text-orange-400"></i>
                                    </div>
                                    <div class="text-xl font-black text-white flex items-baseline gap-1">
                                        <span>{{ $activeSub ? ($activeSub->remaining_kickboxing_classes ?? $activeSub->plan?->kickboxing_classes ?? 0) : 0 }}</span>
                                        <span class="text-xs font-bold text-gray-400">/ {{ $activeSub && $activeSub->plan ? ($activeSub->plan->kickboxing_classes ?? 0) : 0 }} Classes</span>
                                    </div>
                                    <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-orange-400 h-full rounded-full" style="width: 100%"></div>
                                    </div>
                                </div>

                                <!-- Card 6: Nutrition Plans -->
                                <div class="glass-card p-5 rounded-2xl border border-white/5 space-y-3 relative overflow-hidden">
                                    <div class="flex items-center justify-between text-gray-400 text-[10px] font-bold uppercase">
                                        <span>Nutrition Plans</span>
                                        <i class="ri-restaurant-fill text-base text-yellow-400"></i>
                                    </div>
                                    <div class="text-xl font-black text-white flex items-baseline gap-1">
                                        <span>{{ $activeSub ? ($activeSub->remaining_nutrition_plans ?? $activeSub->plan?->nutrition_plans ?? 0) : 0 }}</span>
                                        <span class="text-xs font-bold text-gray-400">/ {{ $activeSub && $activeSub->plan ? ($activeSub->plan->nutrition_plans ?? 0) : 0 }} Plans</span>
                                    </div>
                                    <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-yellow-400 h-full rounded-full" style="width: 100%"></div>
                                    </div>
                                </div>

                                <!-- Card 7: SPA Access -->
                                <div class="glass-card p-5 rounded-2xl border border-white/5 space-y-3 relative overflow-hidden">
                                    <div class="flex items-center justify-between text-gray-400 text-[10px] font-bold uppercase">
                                        <span>SPA & Sauna</span>
                                        <i class="ri-sparkles-line text-base text-amber-300"></i>
                                    </div>
                                    @if($activeSub && $activeSub->plan?->spa_access)
                                        <div class="text-sm font-black text-white flex items-baseline gap-1 truncate">
                                            <span class="text-amber-300">Unlimited</span>
                                            <span class="text-[10px] font-bold text-gray-400">/ Included</span>
                                        </div>
                                        <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-amber-300 h-full rounded-full" style="width: 100%"></div>
                                        </div>
                                    @else
                                        <div class="text-sm font-black text-gray-400 flex items-baseline gap-1 truncate">
                                            <span class="text-gray-500">Not Included</span>
                                            <span class="text-[10px] font-bold text-gray-600">/ Locked</span>
                                        </div>
                                        <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-gray-600 h-full rounded-full" style="width: 0%"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Active Assigned Training Routine & Diet Plan Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Active Workout Routine -->
                            <div class="glass-card p-6 rounded-2xl border border-white/5 space-y-4 relative overflow-hidden flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-[#ff5b00] uppercase tracking-wider flex items-center gap-1.5">
                                            <i class="ri-dumbbell-line"></i> My Assigned Workout Routine
                                        </span>
                                        @if($user->activeWorkoutRoutine)
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Active Plan</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-gray-500/20 text-gray-400 border border-gray-500/30">Pending Trainer</span>
                                        @endif
                                    </div>
                                    @if($user->activeWorkoutRoutine)
                                        <h4 class="text-xl font-black text-white uppercase">{{ $user->activeWorkoutRoutine->title }}</h4>
                                        <p class="text-xs text-gray-400 line-clamp-2">{{ $user->activeWorkoutRoutine->description ?? 'Custom workout program assigned by your personal coach.' }}</p>
                                        <div class="flex items-center gap-4 text-xs text-gray-300 font-mono">
                                            <span><i class="ri-fire-fill text-[#ff5b00]"></i> {{ $user->activeWorkoutRoutine->exercises->count() }} Exercises</span>
                                            <span><i class="ri-shield-user-line text-blue-400"></i> Coach {{ $user->activeWorkoutRoutine->trainer->name ?? 'Trainer' }}</span>
                                        </div>
                                    @else
                                        <div class="py-4 text-center text-gray-400 text-xs italic">
                                            No custom workout routine assigned by your coach yet.
                                        </div>
                                    @endif
                                </div>
                                @if($user->activeWorkoutRoutine)
                                    <button type="button" @click="showWorkoutModal = true" class="w-full py-2.5 px-4 rounded-xl bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/40 hover:bg-[#ff5b00] hover:text-white transition text-xs font-bold text-center block">
                                        View Full Workout Routine &rarr;
                                    </button>
                                @endif
                            </div>

                            <!-- Active Diet Plan -->
                            <div class="glass-card p-6 rounded-2xl border border-white/5 space-y-4 relative overflow-hidden flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                                            <i class="ri-restaurant-line"></i> My Assigned Nutrition Diet
                                        </span>
                                        @if($user->activeDietPlan)
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Active Plan</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-gray-500/20 text-gray-400 border border-gray-500/30">Pending Nutritionist</span>
                                        @endif
                                    </div>
                                    @if($user->activeDietPlan)
                                        <h4 class="text-xl font-black text-white uppercase">{{ $user->activeDietPlan->title }}</h4>
                                        <p class="text-xs text-gray-400 line-clamp-2">{{ $user->activeDietPlan->notes ?? 'Custom nutrition diet plan assigned for your fitness targets.' }}</p>
                                        <div class="flex items-center gap-4 text-xs text-gray-300 font-mono">
                                            <span><i class="ri-goblet-fill text-emerald-400"></i> {{ $user->activeDietPlan->daily_calories }} Kcal / Day</span>
                                            <span><i class="ri-restaurant-2-fill text-yellow-400"></i> {{ $user->activeDietPlan->meals->count() }} Daily Meals</span>
                                        </div>
                                    @else
                                        <div class="py-4 text-center text-gray-400 text-xs italic">
                                            No custom nutrition diet plan assigned by your coach yet.
                                        </div>
                                    @endif
                                </div>
                                @if($user->activeDietPlan)
                                    <button type="button" @click="showDietModal = true" class="w-full py-2.5 px-4 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 hover:bg-emerald-500 hover:text-white transition text-xs font-bold text-center block">
                                        View Full Nutrition Plan &rarr;
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Interactive Workout Routine Modal -->
                        @if($user->activeWorkoutRoutine)
                            <div x-show="showWorkoutModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/80 backdrop-blur-md" @keydown.escape.window="showWorkoutModal = false">
                                <div class="bg-[#12141c] border border-[#ff5b00]/40 rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-6 text-left relative" @click.away="showWorkoutModal = false">
                                    <div class="flex items-center justify-between border-b border-white/10 pb-4">
                                        <div>
                                            <span class="text-xs font-bold text-[#ff5b00] uppercase tracking-wider block">Assigned Workout Routine</span>
                                            <h3 class="text-xl font-black text-white uppercase">{{ $user->activeWorkoutRoutine->title }}</h3>
                                        </div>
                                        <button @click="showWorkoutModal = false" class="text-gray-400 hover:text-white text-xl">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>

                                    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                                        @if($user->activeWorkoutRoutine->description)
                                            <p class="text-xs text-gray-300 italic bg-white/5 p-3 rounded-xl border border-white/5">{{ $user->activeWorkoutRoutine->description }}</p>
                                        @endif

                                        <div class="space-y-3">
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Exercise Plan & Sets breakdown:</h4>
                                            @foreach($user->activeWorkoutRoutine->exercises as $ex)
                                                <div class="p-3.5 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between">
                                                    <div>
                                                        <div class="font-bold text-white text-sm flex items-center gap-2">
                                                            <i class="ri-checkbox-blank-circle-fill text-[8px] text-[#ff5b00]"></i>
                                                            <span>{{ $ex->exercise_name }}</span>
                                                        </div>
                                                        <div class="text-[10px] text-gray-400 font-mono mt-0.5">
                                                            Day: <span class="text-gray-200">{{ $ex->day_name }}</span> | Target: <span class="text-[#ff5b00]">{{ $ex->target_muscle ?? 'General' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <span class="px-2.5 py-1 rounded-lg bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 text-xs font-black">
                                                            {{ $ex->sets }} Sets × {{ $ex->reps }}
                                                        </span>
                                                        @if($ex->rest_seconds)
                                                            <div class="text-[9px] text-gray-400 font-mono mt-0.5">{{ $ex->rest_seconds }}s Rest</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="pt-4 border-t border-white/10 flex justify-end">
                                        <button @click="showWorkoutModal = false" class="px-5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase">
                                            Close Window
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Interactive Diet Plan Modal -->
                        @if($user->activeDietPlan)
                            <div x-show="showDietModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/80 backdrop-blur-md" @keydown.escape.window="showDietModal = false">
                                <div class="bg-[#12141c] border border-emerald-500/40 rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-6 text-left relative" @click.away="showDietModal = false">
                                    <div class="flex items-center justify-between border-b border-white/10 pb-4">
                                        <div>
                                            <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider block">Assigned Nutrition Diet</span>
                                            <h3 class="text-xl font-black text-white uppercase">{{ $user->activeDietPlan->title }}</h3>
                                        </div>
                                        <button @click="showDietModal = false" class="text-gray-400 hover:text-white text-xl">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>

                                    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs font-bold">
                                            <span>Daily Target: {{ $user->activeDietPlan->daily_calories }} Kcal / Day</span>
                                            <span>Total Meals: {{ $user->activeDietPlan->meals->count() }} Meals</span>
                                        </div>

                                        @if($user->activeDietPlan->notes)
                                            <p class="text-xs text-gray-300 italic bg-white/5 p-3 rounded-xl border border-white/5">{{ $user->activeDietPlan->notes }}</p>
                                        @endif

                                        <div class="space-y-3">
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Daily Meals Breakdown:</h4>
                                            @foreach($user->activeDietPlan->meals as $meal)
                                                <div class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-2">
                                                    <div class="flex items-center justify-between border-b border-white/5 pb-2">
                                                        <div class="font-bold text-white text-sm flex items-center gap-2">
                                                            <i class="ri-restaurant-2-fill text-emerald-400"></i>
                                                            <span>{{ $meal->meal_name }}</span>
                                                        </div>
                                                        @if($meal->calories)
                                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                                                {{ $meal->calories }} Kcal
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($meal->ingredients)
                                                        <p class="text-xs text-gray-300 font-mono">{{ $meal->ingredients }}</p>
                                                    @endif
                                                    @if($meal->instructions)
                                                        <p class="text-[11px] text-gray-400 italic">Instruction: {{ $meal->instructions }}</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="pt-4 border-t border-white/10 flex justify-end">
                                        <button @click="showDietModal = false" class="px-5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase">
                                            Close Window
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Member Upcoming PT Sessions Table -->
                <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-white/10 pb-4">
                        <div>
                            <h3 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                                <i class="ri-calendar-check-line neon-accent"></i> My Booked 1-on-1 PT Sessions
                            </h3>
                            <p class="text-xs text-gray-400 mt-1">Sessions booked with your personal trainer</p>
                        </div>
                        <a href="{{ route('bookings.index') }}" class="text-xs font-bold text-[#ff5b00] hover:text-white transition">Book New Session &rarr;</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-white/10 text-gray-400 text-xs uppercase tracking-wider font-bold">
                                    <th class="py-3 px-4">Personal Coach</th>
                                    <th class="py-3 px-4">Booking Date</th>
                                    <th class="py-3 px-4">Time Slot</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Quick Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-sm font-semibold">
                                @forelse($upcomingBookings->take(5) as $bk)
                                    @php
                                        $trainer = $bk->trainer;
                                        $cleanTrainerName = preg_replace('/[^\p{L}\p{N}\s]/u', '', $trainer?->name ?? 'Coach');
                                    @endphp
                                    <tr class="hover:bg-white/5 transition">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-neon-gradient flex items-center justify-center font-black text-white text-sm uppercase shadow-md">
                                                    {{ strtoupper(mb_substr($cleanTrainerName, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-white">{{ $cleanTrainerName }}</div>
                                                    <div class="text-[10px] text-gray-400 font-mono">{{ $trainer?->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 font-mono text-gray-300 text-xs">
                                            {{ $bk->booking_date }}
                                        </td>
                                        <td class="py-4 px-4 font-mono text-xs text-[#ff5b00]">
                                            {{ $bk->start_time }} - {{ $bk->end_time }}
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($bk->status === 'confirmed')
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Confirmed</span>
                                            @elseif($bk->status === 'completed')
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-blue-500/20 text-blue-400 border border-blue-500/30">Completed</span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-gray-500/20 text-gray-400 border border-gray-500/30">{{ ucfirst($bk->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <a href="{{ route('chat.start', $bk->trainer_id) }}" class="px-3 py-1.5 rounded-lg bg-neon-gradient text-white text-xs font-black uppercase tracking-wider inline-flex items-center gap-1 shadow-md shadow-[#ff5b00]/20 hover:opacity-90 transition">
                                                <i class="ri-chat-smile-2-line"></i> Chat Coach
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-400 italic">
                                            No PT sessions booked yet. <a href="{{ route('bookings.index') }}" class="text-[#ff5b00] underline font-bold">Book a session now</a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Agreed Membership Contract & Gym Regulations Table Section -->
                <div class="glass-card p-8 rounded-2xl border border-white/5 space-y-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-white/10 pb-4">
                        <div>
                            <h3 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                                <i class="ri-file-shield-line neon-accent"></i> Agreed Gym Terms & Regulations
                            </h3>
                            <p class="text-xs text-gray-400 mt-1">Official membership rules agreed upon at subscription checkout</p>
                        </div>

                        <span class="px-3.5 py-1.5 rounded-full bg-green-500/20 text-green-400 border border-green-500/30 text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
                            <i class="ri-checkbox-circle-fill"></i> Agreed & Accepted
                        </span>
                    </div>

                    <!-- 2-Column Rules Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-300">
                        @foreach($gymRules as $rule)
                            <div class="p-3.5 rounded-xl bg-white/5 border border-white/5 flex items-start gap-3 hover:border-white/10 transition">
                                <span class="px-2 py-0.5 rounded bg-neon-gradient text-white font-black text-[10px] shrink-0 mt-0.5">
                                    #{{ $rule->rule_number }}
                                </span>
                                <p class="leading-relaxed">{{ $rule->rule_text }}</p>
                            </div>
                        @endforeach
                    </div>

                    <!-- Policy Compliance Warning Note Banner -->
                    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-between text-xs text-amber-300 mt-4">
                        <div class="flex items-center gap-3">
                            <i class="ri-shield-user-line text-xl text-amber-400 shrink-0"></i>
                            <span><strong>Important Policy Notice:</strong> All facility rules and conduct policies must be strictly followed. Violation of facility terms may lead to administrative review or temporary membership suspension.</span>
                        </div>
                    </div>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>
