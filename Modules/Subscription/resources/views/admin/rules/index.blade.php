<x-app-layout>
    <x-slot name="title">Gym Terms & Regulations Management</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                    <i class="ri-file-shield-line text-[#ff5b00]"></i> Gym Rules & Regulations Management
                </h2>
                <p class="text-xs text-gray-400 mt-1">Add, update, reorder, and toggle member conduct policies and facility regulations</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3.5 py-1.5 bg-[#ff5b00]/10 text-[#ff5b00] border border-[#ff5b00]/30 rounded-xl text-xs font-black uppercase">
                    Active Rules: {{ $rules->where('is_active', true)->count() }} / {{ $rules->count() }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        showModal: false,
        isEditing: false,
        formAction: '{{ route('admin.rules.store') }}',
        ruleNumber: '{{ ($rules->max('rule_number') ?? 0) + 1 }}',
        ruleText: '',
        sortOrder: '{{ ($rules->max('rule_number') ?? 0) + 1 }}',
        isActive: true,

        openCreateModal() {
            this.isEditing = false;
            this.formAction = '{{ route('admin.rules.store') }}';
            this.ruleNumber = '{{ ($rules->max('rule_number') ?? 0) + 1 }}';
            this.ruleText = '';
            this.sortOrder = this.ruleNumber;
            this.isActive = true;
            this.showModal = true;
        },

        openEditModalFromEl(button) {
            this.isEditing = true;
            this.formAction = '/admin/gym-rules/' + button.getAttribute('data-id');
            this.ruleNumber = button.getAttribute('data-rule-number');
            this.ruleText = button.getAttribute('data-rule-text');
            this.sortOrder = button.getAttribute('data-sort-order');
            this.isActive = button.getAttribute('data-is-active') === '1';
            this.showModal = true;
        }
    }">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-green-500/20 border border-green-500/30 text-green-400 font-bold text-sm flex items-center gap-2">
                <i class="ri-checkbox-circle-fill text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-xl bg-red-500/20 border border-red-500/30 text-red-400 font-bold text-sm space-y-1">
                @foreach($errors->all() as $err)
                    <div class="flex items-center gap-2">
                        <i class="ri-error-warning-fill"></i>
                        <span>{{ $err }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Action & Management Card -->
        <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-white/10 pb-4">
                <div>
                    <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="ri-list-ordered-2 text-[#ff5b00]"></i> Facility Rules List ({{ $rules->count() }})
                    </h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">These rules appear on the checkout page and member dashboard</p>
                </div>
                <button
                    @click="openCreateModal()"
                    type="button"
                    class="px-4 py-2.5 rounded-xl bg-neon-gradient text-white font-black text-xs uppercase tracking-wider bg-neon-glow hover:opacity-90 transition flex items-center gap-2 cursor-pointer shadow-lg"
                >
                    <i class="ri-add-circle-line text-base"></i> + Add New Gym Rule
                </button>
            </div>

            <!-- Rules List -->
            <div class="space-y-4">
                @forelse($rules as $rule)
                    <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 hover:border-[#ff5b00]/40 transition space-y-4">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <!-- Left: Number Badge + Full Rule Text -->
                            <div class="flex items-start gap-4 flex-1 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-neon-gradient flex items-center justify-center font-black text-white text-xs shrink-0 shadow-lg shadow-[#ff5b00]/20">
                                    #{{ $rule->rule_number }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-200 font-medium leading-relaxed break-normal whitespace-normal">
                                        {{ $rule->rule_text }}
                                    </p>
                                    <div class="mt-2 flex items-center gap-3 text-[11px] font-mono text-gray-400">
                                        <span>Sort Order: <strong>{{ $rule->sort_order ?? $rule->rule_number }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Updated: {{ $rule->updated_at?->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Actions Toolbar -->
                            <div class="flex items-center gap-2 shrink-0 self-end lg:self-center pt-3 lg:pt-0 border-t lg:border-t-0 border-white/5">
                                <!-- Toggle Active Status -->
                                <form action="{{ route('admin.rules.toggle', $rule->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        title="Click to toggle status"
                                        class="px-3.5 py-1.5 rounded-xl text-[11px] font-black uppercase tracking-wider border cursor-pointer transition flex items-center gap-1.5 {{ $rule->is_active ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30 hover:bg-emerald-500/30' : 'bg-gray-500/20 text-gray-400 border-gray-500/30 hover:bg-gray-500/30' }}"
                                    >
                                        <i class="{{ $rule->is_active ? 'ri-checkbox-circle-line' : 'ri-close-circle-line' }}"></i>
                                        <span>{{ $rule->is_active ? 'Active' : 'Disabled' }}</span>
                                    </button>
                                </form>

                                <!-- Edit Button -->
                                <button
                                    type="button"
                                    @click="openEditModalFromEl($el)"
                                    data-id="{{ $rule->id }}"
                                    data-rule-number="{{ $rule->rule_number }}"
                                    data-rule-text="{{ e($rule->rule_text) }}"
                                    data-sort-order="{{ $rule->sort_order ?? $rule->rule_number }}"
                                    data-is-active="{{ $rule->is_active ? '1' : '0' }}"
                                    class="px-3.5 py-1.5 rounded-xl bg-white/5 hover:bg-white/15 text-gray-200 hover:text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer border border-white/10"
                                >
                                    <i class="ri-edit-line text-[#ff5b00]"></i> Edit
                                </button>

                                <!-- Delete Button -->
                                <form action="{{ route('admin.rules.destroy', $rule->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete Gym Rule #{{ $rule->rule_number }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="px-3.5 py-1.5 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer border border-red-500/20"
                                    >
                                        <i class="ri-delete-bin-line"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-400 space-y-2">
                        <i class="ri-file-shield-line text-4xl text-gray-500"></i>
                        <p class="text-sm font-bold uppercase tracking-wider">No gym rules created yet</p>
                        <p class="text-xs text-gray-400">Click "+ Add New Gym Rule" to create your first official facility rule.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Create / Edit Modal -->
        <div
            x-show="showModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;"
        >
            <div
                @click.away="showModal = false"
                class="bg-[#12141c] border border-white/20 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-5"
            >
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="ri-file-shield-line text-[#ff5b00]"></i>
                        <span x-text="isEditing ? 'Edit Gym Rule #' + ruleNumber : 'Create New Gym Rule'"></span>
                    </h3>
                    <button @click="showModal = false" type="button" class="text-gray-400 hover:text-white text-xl cursor-pointer">
                        <i class="ri-close-line"></i>
                    </button>
                </div>

                <form :action="formAction" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="isEditing">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Rule #</label>
                            <input
                                type="number"
                                name="rule_number"
                                x-model="ruleNumber"
                                required
                                min="1"
                                class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]"
                                placeholder="e.g. 1"
                            >
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Sort Order</label>
                            <input
                                type="number"
                                name="sort_order"
                                x-model="sortOrder"
                                min="0"
                                class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]"
                                placeholder="e.g. 1"
                            >
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Rule Description Text</label>
                        <textarea
                            name="rule_text"
                            x-model="ruleText"
                            required
                            rows="4"
                            class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00] leading-relaxed"
                            placeholder="State the official gym term, etiquette rule, or conduct guideline clearly..."
                        ></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input
                            type="checkbox"
                            name="is_active"
                            id="is_active"
                            value="1"
                            x-model="isActive"
                            class="rounded bg-[#1a1d28] border-white/20 text-[#ff5b00] focus:ring-[#ff5b00]"
                        >
                        <label for="is_active" class="text-xs font-bold text-gray-300 cursor-pointer">
                            Active (Visible to members on checkout & dashboard)
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-xl bg-neon-gradient text-white font-black text-xs uppercase tracking-wider bg-neon-glow hover:opacity-90 transition shadow-lg cursor-pointer"
                            x-text="isEditing ? 'Save Changes' : 'Create Rule'"
                        ></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
