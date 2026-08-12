<x-app-layout>
    <x-slot name="title">User Management & Security Control</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                    <i class="ri-shield-user-line text-[#ff5b00]"></i> Master User Moderation & Security Panel
                </h2>
                <p class="text-xs text-gray-400 mt-1">Manage platform accounts, role assignments, activation status, and instant security blocks</p>
            </div>
            <div class="px-3.5 py-2 bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30 rounded-xl text-xs font-black uppercase shrink-0">
                Total Registered: {{ $users->total() }} Users
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ activeBlockUserId: null, activeBlockUserName: '' }">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-green-500/20 border border-green-500/30 text-green-400 font-bold text-sm flex items-center gap-2">
                <i class="ri-checkbox-circle-fill text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter & Search Bar -->
        <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-5 shadow-2xl">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                <div class="md:col-span-6 relative w-full">
                    <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or phone number..." class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs pl-10 pr-4 py-3 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]">
                </div>

                <div class="md:col-span-4 w-full">
                    <select name="role" onchange="this.form.submit()" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs px-4 py-3 focus:border-[#ff5b00]">
                        <option value="">-- All Roles --</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admins Only</option>
                        <option value="trainer" {{ request('role') === 'trainer' ? 'selected' : '' }}>Trainers Only</option>
                        <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>Members Only</option>
                    </select>
                </div>

                <div class="md:col-span-2 w-full">
                    <button type="submit" class="w-full py-3 bg-[#ff5b00] hover:bg-[#ff5b00]/90 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg transition cursor-pointer">
                        Filter Results
                    </button>
                </div>
            </form>
        </div>

        <!-- Users Table Card -->
        <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-6">
            <div class="overflow-x-auto rounded-xl border border-white/5">
                <table class="w-full text-left text-xs whitespace-nowrap">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10 text-gray-400 uppercase font-black tracking-wider">
                            <th class="py-3.5 px-4">User ID</th>
                            <th class="py-3.5 px-4">User Profile</th>
                            <th class="py-3.5 px-4">Role</th>
                            <th class="py-3.5 px-4">Active Status</th>
                            <th class="py-3.5 px-4">Security Status</th>
                            <th class="py-3.5 px-4 text-right">Moderation Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 font-semibold text-gray-300">
                        @forelse($users as $u)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-4 px-4 font-mono font-bold text-white">#{{ $u->id }}</td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        @if($u->getFirstMediaUrl('avatar', 'thumb'))
                                            <img src="{{ $u->getFirstMediaUrl('avatar', 'thumb') }}" alt="Avatar" class="w-9 h-9 rounded-xl object-cover border border-[#ff5b00] shrink-0">
                                        @else
                                            <div class="w-9 h-9 rounded-xl bg-neon-gradient flex items-center justify-center font-black text-white text-xs shrink-0">
                                                {{ strtoupper(substr($u->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="font-bold text-white text-sm truncate max-w-[200px]">{{ $u->name }}</div>
                                            <div class="text-gray-400 text-[11px] truncate max-w-[220px]">{{ $u->email }} &bull; {{ $u->phone ?? 'No Phone' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    @php $userRole = $u->getRoleNames()->first() ?? 'member'; @endphp
                                    @if($userRole === 'admin')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30">Master Admin</span>
                                    @elseif($userRole === 'trainer')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#ff5b00]/20 text-[#ff5b00] border border-[#ff5b00]/30">Private Trainer</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30">Member</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($u->is_active)
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-green-500/20 text-green-400 border border-green-500/30">Active</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Frozen</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($u->is_blocked)
                                        <div>
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-600/30 text-red-400 border border-red-500/40">Blocked</span>
                                            <div class="text-[10px] text-gray-400 font-mono mt-1 max-w-xs truncate" title="{{ $u->block_reason }}">
                                                Reason: {{ $u->block_reason }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-green-500/20 text-green-400 border border-green-500/30">Clear / Safe</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Toggle Active Form -->
                                        <form action="{{ route('admin.users.toggle-active', $u->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg {{ $u->is_active ? 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30 hover:bg-yellow-500' : 'bg-green-500/20 text-green-400 border-green-500/30 hover:bg-green-500' }} border hover:text-white font-black uppercase text-[10px] transition cursor-pointer">
                                                {{ $u->is_active ? 'Freeze' : 'Activate' }}
                                            </button>
                                        </form>

                                        <!-- Block / Unblock Action -->
                                        @if($u->is_blocked)
                                            <form action="{{ route('admin.users.unblock', $u->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-green-500 hover:text-white font-black uppercase text-[10px] transition cursor-pointer">
                                                    Unblock
                                                </button>
                                            </form>
                                        @else
                                            @if(!$u->hasRole('admin'))
                                                <button @click="activeBlockUserId = {{ $u->id }}; activeBlockUserName = '{{ addslashes($u->name) }}'" type="button" class="px-3 py-1.5 rounded-lg bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500 hover:text-white font-black uppercase text-[10px] transition cursor-pointer">
                                                    Block
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400 italic">
                                    No user accounts found matching query.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-4">
                {{ $users->withQueryString()->links() }}
            </div>
        </div>

        <!-- Block User Reason Modal -->
        <div x-show="activeBlockUserId !== null" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/85 backdrop-blur-md" style="display: none;">
            <div @click.away="activeBlockUserId = null" class="w-full max-w-md bg-[#12141c] border border-white/15 rounded-2xl p-6 shadow-2xl space-y-5 text-left font-sans">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <h3 class="text-lg font-black text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="ri-shield-keyhole-line text-red-500"></i> Suspend & Block User
                        </h3>
                        <p class="text-xs text-gray-400 mt-1" x-text="'Block user account: ' + activeBlockUserName"></p>
                    </div>
                    <button @click="activeBlockUserId = null" type="button" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white flex items-center justify-center transition cursor-pointer">
                        <i class="ri-close-line text-lg"></i>
                    </button>
                </div>

                <form :action="'{{ url('admin/users') }}/' + activeBlockUserId + '/block'" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Reason for Suspension / Block *</label>
                        <input type="text" name="block_reason" required placeholder="e.g. Terms of Service Violation / Abusive behavior" class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-red-500 focus:ring-1 focus:ring-red-500">
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" @click="activeBlockUserId = null" class="w-1/2 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-black text-xs uppercase tracking-wider transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider transition shadow-lg cursor-pointer">
                            Confirm Security Block
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
