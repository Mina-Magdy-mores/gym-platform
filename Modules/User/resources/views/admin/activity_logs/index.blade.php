<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-white tracking-wider uppercase flex items-center gap-2">
                    <i class="ri-shield-check-fill text-[#ff5b00]"></i>
                    <span>Audit Logs & Security Trail</span>
                </h2>
                <p class="text-xs text-gray-400 mt-1">Real-time immutable audit trail for administrative actions, pricing edits, payouts, and user status modifications.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-gray-300 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Live Tracking (Spatie v5)</span>
                </span>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        selectedLog: null,
        showDiffModal: false,
        openDiff(log) {
            this.selectedLog = log;
            this.showDiffModal = true;
        }
    }" class="space-y-6">

        <!-- 1. STATISTICAL CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Logs -->
            <div class="glass-card p-4 rounded-2xl border border-white/5 relative overflow-hidden group hover:border-[#ff5b00]/30 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Total Audit Logs</p>
                        <h3 class="text-2xl font-black text-white mt-1">{{ number_format($totalLogs) }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-neon-gradient/10 border border-[#ff5b00]/20 flex items-center justify-center text-[#ff5b00] text-lg">
                        <i class="ri-database-2-line"></i>
                    </div>
                </div>
            </div>

            <!-- Today's Events -->
            <div class="glass-card p-4 rounded-2xl border border-white/5 relative overflow-hidden group hover:border-sky-500/30 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Today's Activity</p>
                        <h3 class="text-2xl font-black text-sky-400 mt-1">{{ number_format($todayLogs) }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 text-lg">
                        <i class="ri-history-line"></i>
                    </div>
                </div>
            </div>

            <!-- Critical Financial & User Actions -->
            <div class="glass-card p-4 rounded-2xl border border-white/5 relative overflow-hidden group hover:border-amber-500/30 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Critical Core Events</p>
                        <h3 class="text-2xl font-black text-amber-400 mt-1">{{ number_format($criticalLogs) }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-lg">
                        <i class="ri-alert-line"></i>
                    </div>
                </div>
            </div>

            <!-- Active Causers -->
            <div class="glass-card p-4 rounded-2xl border border-white/5 relative overflow-hidden group hover:border-purple-500/30 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Active Operators</p>
                        <h3 class="text-2xl font-black text-purple-400 mt-1">{{ number_format($uniqueCausersCount) }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 text-lg">
                        <i class="ri-user-star-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. SEARCH & FILTER BAR -->
        <div class="glass-card p-4 rounded-2xl border border-white/5">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Keyword Search -->
                <div class="relative">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user, ID, description..." class="w-full bg-[#12141c] border border-white/10 rounded-xl pl-9 pr-3 py-2 text-xs text-white placeholder-gray-500 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]">
                </div>

                <!-- Domain Filter -->
                <div>
                    <select name="log_name" class="w-full bg-[#12141c] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]">
                        <option value="">All Domains (Modules)</option>
                        @foreach($availableLogNames as $logName)
                            <option value="{{ $logName }}" {{ request('log_name') === $logName ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $logName)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Event Filter -->
                <div>
                    <select name="event" class="w-full bg-[#12141c] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]">
                        <option value="">All Events</option>
                        <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>Created (Insert)</option>
                        <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>Updated (Modify)</option>
                        <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>Deleted (Remove)</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <input type="date" name="date" value="{{ request('date') }}" class="w-full bg-[#12141c] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]">
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 bg-neon-gradient hover:opacity-90 text-white font-bold py-2 px-4 rounded-xl text-xs flex items-center justify-center gap-1 shadow-lg shadow-[#ff5b00]/20 transition">
                        <i class="ri-filter-3-line"></i>
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['search', 'log_name', 'event', 'date', 'causer_id']))
                        <a href="{{ route('admin.activity-logs.index') }}" class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white transition" title="Clear Filters">
                            <i class="ri-refresh-line"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 3. LOGS AUDIT TABLE -->
        <div class="glass-card rounded-2xl border border-white/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-300">
                    <thead class="bg-[#12141c]/80 text-[10px] uppercase font-black tracking-wider text-gray-400 border-b border-white/5">
                        <tr>
                            <th class="py-3 px-4">Operator (Causer)</th>
                            <th class="py-3 px-4">Domain & Event</th>
                            <th class="py-3 px-4">Subject & Description</th>
                            <th class="py-3 px-4">Changes Recorded</th>
                            <th class="py-3 px-4">Timestamp</th>
                            <th class="py-3 px-4 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($logs as $log)
                            @php
                                $eventColor = match($log->event) {
                                    'created' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                    'updated' => 'bg-sky-500/10 text-sky-400 border-sky-500/20',
                                    'deleted' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                                    default => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
                                };
                                $properties = $log->properties ?? collect();
                                $hasChanges = !empty($properties['attributes']) || !empty($properties['old']);
                            @endphp
                            <tr class="hover:bg-white/[0.02] transition">
                                <!-- Operator -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($log->causer)
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-neon-gradient flex items-center justify-center text-white font-bold text-[10px] shrink-0">
                                                {{ strtoupper(substr($log->causer->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-white leading-tight">{{ $log->causer->name }}</p>
                                                <span class="text-[10px] text-gray-500">{{ $log->causer->roles->first()?->name ?? 'User' }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white/5 border border-white/10 text-[10px] text-gray-400 font-semibold">
                                            <i class="ri-robot-line"></i> System Engine
                                        </span>
                                    @endif
                                </td>

                                <!-- Domain & Event -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $eventColor }}">
                                            {{ $log->event ?? 'Action' }}
                                        </span>
                                        <span class="text-xs font-semibold text-gray-300">
                                            {{ ucfirst(str_replace('_', ' ', $log->log_name ?? 'General')) }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Subject & Description -->
                                <td class="py-3.5 px-4 max-w-xs">
                                    <p class="text-xs text-white font-medium truncate" title="{{ $log->description }}">
                                        {{ $log->description ?: ($log->event . ' ' . $log->log_name) }}
                                    </p>
                                    <span class="text-[10px] text-gray-500">
                                        ID: #{{ $log->subject_id ?? 'N/A' }} 
                                        @if($log->subject_type)
                                            ({{ class_basename($log->subject_type) }})
                                        @endif
                                    </span>
                                </td>

                                <!-- Changes Summary -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($hasChanges)
                                        @php
                                            $changedFields = array_keys($properties['attributes'] ?? []);
                                        @endphp
                                        <div class="flex flex-wrap gap-1 max-w-[200px]">
                                            @foreach(array_slice($changedFields, 0, 3) as $field)
                                                <span class="px-1.5 py-0.5 rounded bg-white/5 text-[10px] text-gray-300 font-mono border border-white/5">
                                                    {{ $field }}
                                                </span>
                                            @endforeach
                                            @if(count($changedFields) > 3)
                                                <span class="text-[10px] text-gray-500 font-bold">+{{ count($changedFields) - 3 }} more</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-500 text-[10px]">No modified properties</span>
                                    @endif
                                </td>

                                <!-- Timestamp -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <p class="text-xs text-gray-300 font-mono">{{ $log->created_at->format('Y-m-d H:i:s') }}</p>
                                    <span class="text-[10px] text-[#ff5b00] font-semibold">{{ $log->created_at->diffForHumans() }}</span>
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    @if($hasChanges)
                                        <button 
                                            type="button" 
                                            @click='openDiff(@json($log))'
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-white/5 hover:bg-neon-gradient text-gray-300 hover:text-white text-xs font-bold transition border border-white/10 hover:border-transparent cursor-pointer shadow-sm"
                                        >
                                            <i class="ri-git-commit-line text-sm"></i>
                                            <span>View Diff</span>
                                        </button>
                                    @else
                                        <span class="text-gray-600 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12">
                                    <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-500 text-2xl mx-auto mb-3">
                                        <i class="ri-history-line"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-white uppercase tracking-wider">No Activity Logs Found</h4>
                                    <p class="text-xs text-gray-500 mt-1">Try adjusting your filters or search keywords.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="p-4 border-t border-white/5">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

        <!-- 4. INTERACTIVE DIFF MODAL (Alpine.js) -->
        <div 
            x-show="showDiffModal" 
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
        >
            <div 
                @click.outside="showDiffModal = false"
                class="w-full max-w-3xl glass-card bg-[#12141c]/95 border border-white/10 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh]"
            >
                <!-- Modal Header -->
                <div class="p-5 border-b border-white/10 flex items-center justify-between bg-white/[0.02]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-neon-gradient flex items-center justify-center text-white text-lg font-black shrink-0">
                            <i class="ri-git-merge-line"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                                <span>Audit Change Diff</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase" :class="{
                                    'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20': selectedLog?.event === 'created',
                                    'bg-sky-500/10 text-sky-400 border border-sky-500/20': selectedLog?.event === 'updated',
                                    'bg-rose-500/10 text-rose-400 border border-rose-500/20': selectedLog?.event === 'deleted'
                                }" x-text="selectedLog?.event"></span>
                            </h3>
                            <p class="text-xs text-gray-400" x-text="selectedLog?.description"></p>
                        </div>
                    </div>
                    <button @click="showDiffModal = false" type="button" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white flex items-center justify-center transition">
                        <i class="ri-close-line text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body: Diff Content -->
                <div class="p-5 overflow-y-auto space-y-4 custom-scrollbar">
                    <!-- Meta Details -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs bg-[#0b0d14] p-3 rounded-2xl border border-white/5">
                        <div>
                            <span class="text-[10px] text-gray-500 font-bold block uppercase">Domain</span>
                            <span class="text-white font-semibold" x-text="selectedLog?.log_name"></span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-500 font-bold block uppercase">Subject ID</span>
                            <span class="text-white font-semibold font-mono" x-text="'#' + selectedLog?.subject_id"></span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-500 font-bold block uppercase">Causer</span>
                            <span class="text-white font-semibold" x-text="selectedLog?.causer ? selectedLog?.causer.name : 'System Engine'"></span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-500 font-bold block uppercase">Timestamp</span>
                            <span class="text-white font-mono text-[11px]" x-text="selectedLog?.created_at ? new Date(selectedLog?.created_at).toLocaleString() : ''"></span>
                        </div>
                    </div>

                    <!-- Side-by-Side Diff Table -->
                    <div class="border border-white/10 rounded-2xl overflow-hidden">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#0b0d14] text-[10px] uppercase font-black text-gray-400 border-b border-white/5">
                                <tr>
                                    <th class="py-2.5 px-4 w-1/4">Field (Property)</th>
                                    <th class="py-2.5 px-4 w-3/8 text-rose-400">Old Value (Before)</th>
                                    <th class="py-2.5 px-4 w-3/8 text-emerald-400">New Value (After)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 bg-[#12141c]">
                                <template x-if="selectedLog?.properties?.attributes">
                                    <template x-for="(newValue, field) in selectedLog.properties.attributes" :key="field">
                                        <tr class="hover:bg-white/[0.02]">
                                            <td class="py-3 px-4 font-mono font-bold text-gray-300" x-text="field"></td>
                                            <!-- Old Value -->
                                            <td class="py-3 px-4 font-mono text-rose-300/80 bg-rose-500/[0.03]">
                                                <span x-text="selectedLog?.properties?.old ? (typeof selectedLog.properties.old[field] === 'object' ? JSON.stringify(selectedLog.properties.old[field]) : selectedLog.properties.old[field]) : '(none)'"></span>
                                            </td>
                                            <!-- New Value -->
                                            <td class="py-3 px-4 font-mono text-emerald-300 font-bold bg-emerald-500/[0.03]">
                                                <span x-text="typeof newValue === 'object' ? JSON.stringify(newValue) : newValue"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-white/10 flex justify-end bg-white/[0.02]">
                    <button @click="showDiffModal = false" type="button" class="px-5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
