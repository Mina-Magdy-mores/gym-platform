<x-app-layout>
    <x-slot name="title">Trainer Recruitment & Credentials Management</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-black text-white uppercase tracking-wide flex items-center gap-2">
                    <i class="ri-user-heart-line text-[#ff5b00]"></i> Certified Coaches & Recruitment Oversight
                </h2>
                <p class="text-xs text-gray-400 mt-1">Review trainer dossiers, verify certification documents, onboard new coaches, and monitor performance</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3.5 py-1.5 bg-[#ff5b00]/10 text-[#ff5b00] border border-[#ff5b00]/30 rounded-xl text-xs font-black uppercase">
                    Total Coaches: {{ $trainers->total() }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        showHireModal: false,
        showPromoteModal: false,
        showCertModal: false,
        showPreviewModal: false,
        activeTrainerId: null,
        activeTrainerName: '',
        previewUrl: '',
        previewName: '',
        previewIsPdf: false,

        openUploadCertModal(id, name) {
            this.activeTrainerId = id;
            this.activeTrainerName = name;
            this.showCertModal = true;
        },

        previewDoc(url, name) {
            this.previewUrl = url;
            this.previewName = name;
            this.previewIsPdf = url.toLowerCase().endsWith('.pdf');
            this.showPreviewModal = true;
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

        <!-- Action Bar -->
        <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="ri-shield-star-line text-[#ff5b00]"></i> Coaching Staff Roster ({{ $trainers->total() }})
                </h3>
                <p class="text-[11px] text-gray-400 mt-0.5">Manage professional accreditation, credentials, and access permissions</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <button
                    @click="showPromoteModal = true"
                    type="button"
                    class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition flex items-center gap-2 cursor-pointer border border-white/10"
                >
                    <i class="ri-user-shared-line text-emerald-400"></i> Promote Member to Coach
                </button>
                <button
                    @click="showHireModal = true"
                    type="button"
                    class="px-4 py-2.5 rounded-xl bg-neon-gradient text-white font-black text-xs uppercase tracking-wider bg-neon-glow hover:opacity-90 transition flex items-center gap-2 cursor-pointer shadow-lg"
                >
                    <i class="ri-user-add-line text-base"></i> + Recruit New Coach
                </button>
            </div>
        </div>

        <!-- Trainers Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($trainers as $t)
                @php
                    $cleanName = preg_replace('/^(Captain|Coach)\s+/i', '', $t->name);
                    $certificates = $t->getMedia('certificates');
                @endphp
                <div class="bg-[#12141c]/90 backdrop-blur-md rounded-2xl border border-white/10 p-6 shadow-2xl space-y-5 hover:border-[#ff5b00]/40 transition duration-200 flex flex-col justify-between">
                    <div class="space-y-4">
                        <!-- Coach Header & Avatar -->
                        <div class="flex items-center gap-4 pb-4 border-b border-white/10">
                            @if($t->hasMedia('avatar'))
                                <img src="{{ $t->getFirstMediaUrl('avatar', 'thumb') }}" alt="{{ $t->name }}" class="w-14 h-14 rounded-2xl object-cover border-2 border-[#ff5b00] shadow-lg shrink-0">
                            @else
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-sky-600 to-indigo-700 flex items-center justify-center font-black text-white text-xl shadow-lg shrink-0 uppercase">
                                    {{ mb_substr($cleanName, 0, 1) }}
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h3 class="font-black text-white text-base tracking-wide truncate">{{ $t->name }}</h3>
                                    @if($t->is_active && !$t->is_blocked)
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block shadow-[0_0_8px_#34d399]" title="Active"></span>
                                    @elseif(!$t->is_active)
                                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-yellow-500/20 text-yellow-400">Frozen</span>
                                    @endif
                                </div>
                                <p class="text-gray-400 text-xs truncate">{{ $t->email }}</p>
                                <p class="text-[11px] font-mono text-gray-500">{{ $t->phone ?? 'No Phone Provided' }}</p>
                            </div>
                        </div>

                        <!-- Stats Quick Metrics -->
                        <div class="grid grid-cols-2 gap-2 text-xs font-mono">
                            <div class="p-2.5 rounded-xl bg-white/[0.03] border border-white/5 space-y-0.5">
                                <span class="text-[10px] text-gray-400 font-sans uppercase font-bold block">Sessions Completed</span>
                                <span class="font-black text-white text-sm">{{ $t->completed_sessions_count ?? 0 }}</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-white/[0.03] border border-white/5 space-y-0.5">
                                <span class="text-[10px] text-gray-400 font-sans uppercase font-bold block">Wallet Balance</span>
                                <span class="font-black text-emerald-400 text-sm">{{ number_format($t->trainerWallet->balance ?? 0, 2) }} EGP</span>
                            </div>
                        </div>

                        <!-- Accreditation & Certificates Dossier -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-black text-gray-300 uppercase tracking-wider flex items-center gap-1">
                                    <i class="ri-file-shield-line text-[#ff5b00]"></i> Certificates & Files ({{ $certificates->count() }})
                                </span>
                                <button
                                    @click="openUploadCertModal({{ $t->id }}, '{{ addslashes($t->name) }}')"
                                    type="button"
                                    class="text-[10px] font-bold text-[#ff5b00] hover:text-white transition cursor-pointer"
                                >
                                    + Upload More
                                </button>
                            </div>

                            @if($certificates->isNotEmpty())
                                <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                                    @foreach($certificates as $cert)
                                        <div class="p-2 rounded-lg bg-white/5 border border-white/5 flex items-center justify-between gap-2 text-xs hover:bg-white/10 transition">
                                            <button
                                                type="button"
                                                @click="previewDoc('{{ $cert->getUrl() }}', '{{ addslashes($cert->file_name) }}')"
                                                class="flex items-center gap-2 min-w-0 text-gray-200 hover:text-[#ff5b00] transition truncate text-left cursor-pointer"
                                            >
                                                @if(str_ends_with(strtolower($cert->file_name), '.pdf'))
                                                    <i class="ri-file-pdf-line text-red-400 shrink-0 text-sm"></i>
                                                @else
                                                    <i class="ri-image-line text-sky-400 shrink-0 text-sm"></i>
                                                @endif
                                                <span class="truncate font-medium text-[11px]">{{ $cert->file_name }}</span>
                                            </button>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <a
                                                    href="{{ $cert->getUrl() }}"
                                                    download
                                                    class="text-gray-400 hover:text-white text-xs p-1"
                                                    title="Download Document"
                                                >
                                                    <i class="ri-download-2-line"></i>
                                                </a>
                                                <form action="{{ route('admin.trainers.delete-certificate', $cert->id) }}" method="POST" onsubmit="return confirm('Remove this certificate document?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-gray-500 hover:text-red-400 text-xs cursor-pointer p-1" title="Delete">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-2.5 rounded-xl bg-white/[0.02] border border-dashed border-white/10 text-center text-[10px] text-gray-400">
                                    No certificate documents on file yet.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Coach Footer Actions -->
                    <div class="pt-4 border-t border-white/10 flex items-center justify-between gap-2">
                        <!-- Status Toggle -->
                        <form action="{{ route('admin.users.toggle-active', $t->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button
                                type="submit"
                                class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider border transition cursor-pointer {{ $t->is_active ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30 hover:bg-emerald-500/30' : 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30 hover:bg-yellow-500/30' }}"
                            >
                                {{ $t->is_active ? 'Active' : 'Frozen' }}
                            </button>
                        </form>

                        <div class="flex items-center gap-2">
                            <a
                                href="{{ route('chat.show', $t->id) }}"
                                class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/15 text-gray-300 hover:text-white text-[11px] font-bold transition flex items-center gap-1 border border-white/10"
                            >
                                <i class="ri-chat-smile-2-line text-[#ff5b00]"></i> Chat
                            </a>

                            <!-- Delete / Dismiss Coach -->
                            <form action="{{ route('admin.trainers.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to dismiss {{ $t->name }} from the coaching roster?');">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="px-2.5 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 text-[11px] font-bold transition flex items-center gap-1 border border-red-500/20 cursor-pointer"
                                    title="Dismiss Coach"
                                >
                                    <i class="ri-user-unfollow-line"></i> Dismiss
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-gray-400 space-y-2">
                    <i class="ri-user-heart-line text-4xl text-gray-500"></i>
                    <p class="text-sm font-bold uppercase tracking-wider">No certified trainers registered</p>
                    <p class="text-xs text-gray-400">Click "+ Recruit New Coach" or "Promote Member" to add your first trainer.</p>
                </div>
            @endforelse
        </div>

        @if($trainers->hasPages())
            <div class="p-4">
                {{ $trainers->links() }}
            </div>
        @endif

        <!-- 1. Recruit New Coach Modal -->
        <div
            x-show="showHireModal"
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
                @click.away="showHireModal = false"
                class="bg-[#12141c] border border-white/20 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto"
            >
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="ri-user-add-line text-[#ff5b00]"></i> Recruit & Onboard Coach
                    </h3>
                    <button @click="showHireModal = false" type="button" class="text-gray-400 hover:text-white text-xl cursor-pointer">
                        <i class="ri-close-line"></i>
                    </button>
                </div>

                <form action="{{ route('admin.trainers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Full Name *</label>
                        <input
                            type="text"
                            name="name"
                            required
                            class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]"
                            placeholder="e.g. Captain Tarek Nour"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Email Address *</label>
                            <input
                                type="email"
                                name="email"
                                required
                                class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]"
                                placeholder="coach@fitclub.com"
                            >
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Phone Number</label>
                            <input
                                type="text"
                                name="phone"
                                class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]"
                                placeholder="01012345678"
                            >
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Initial Password *</label>
                        <input
                            type="password"
                            name="password"
                            required
                            minlength="8"
                            class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]"
                            placeholder="Min. 8 characters"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Profile Avatar Picture</label>
                        <input
                            type="file"
                            name="avatar"
                            accept="image/*"
                            class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-gray-400 text-xs p-2.5 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#ff5b00] file:text-white cursor-pointer"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Certificates & Accreditations (PDF/Images)</label>
                        <input
                            type="file"
                            name="certificates[]"
                            multiple
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-gray-400 text-xs p-2.5 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-white/20 file:text-white cursor-pointer"
                        >
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                        <button
                            type="button"
                            @click="showHireModal = false"
                            class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-xl bg-neon-gradient text-white font-black text-xs uppercase tracking-wider bg-neon-glow hover:opacity-90 transition shadow-lg cursor-pointer"
                        >
                            Hire Coach & Establish Wallet
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. Promote Existing Member Modal -->
        <div
            x-show="showPromoteModal"
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
                @click.away="showPromoteModal = false"
                class="bg-[#12141c] border border-white/20 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-5"
            >
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="ri-user-shared-line text-emerald-400"></i> Promote Existing Member to Coach
                    </h3>
                    <button @click="showPromoteModal = false" type="button" class="text-gray-400 hover:text-white text-xl cursor-pointer">
                        <i class="ri-close-line"></i>
                    </button>
                </div>

                <form action="{{ route('admin.trainers.promote') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Select Registered User *</label>
                        <select
                            name="user_id"
                            required
                            class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-white text-xs p-3 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00]"
                        >
                            <option value="">-- Choose User to Promote --</option>
                            @foreach($availableMembers as $mem)
                                <option value="{{ $mem->id }}">{{ $mem->name }} ({{ $mem->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-black text-gray-300 uppercase tracking-wider">Upload Certification Documents (Optional)</label>
                        <input
                            type="file"
                            name="certificates[]"
                            multiple
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-gray-400 text-xs p-2.5 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-500 file:text-white cursor-pointer"
                        >
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                        <button
                            type="button"
                            @click="showPromoteModal = false"
                            class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs uppercase tracking-wider transition shadow-lg cursor-pointer"
                        >
                            Promote to Certified Trainer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 3. Upload Additional Certificates Modal -->
        <div
            x-show="showCertModal"
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
                @click.away="showCertModal = false"
                class="bg-[#12141c] border border-white/20 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-5"
            >
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="ri-file-shield-line text-[#ff5b00]"></i> Upload Certificates
                    </h3>
                    <button @click="showCertModal = false" type="button" class="text-gray-400 hover:text-white text-xl cursor-pointer">
                        <i class="ri-close-line"></i>
                    </button>
                </div>

                <form :action="'/admin/trainers/' + activeTrainerId + '/certificates'" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <p class="text-xs text-gray-300">
                        Upload official accreditation or course certificates for <strong class="text-white" x-text="activeTrainerName"></strong>.
                    </p>

                    <div class="space-y-1">
                        <input
                            type="file"
                            name="certificates[]"
                            multiple
                            required
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full bg-[#1a1d28] border border-white/10 rounded-xl text-gray-400 text-xs p-2.5 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#ff5b00] file:text-white cursor-pointer"
                        >
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-white/10">
                        <button
                            type="button"
                            @click="showCertModal = false"
                            class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2 rounded-xl bg-neon-gradient text-white font-black text-xs uppercase tracking-wider bg-neon-glow hover:opacity-90 transition shadow-lg cursor-pointer"
                        >
                            Upload Files
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 4. Certificate Document Preview Lightbox Modal -->
        <div
            x-show="showPreviewModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md"
            style="display: none;"
        >
            <div
                @click.away="showPreviewModal = false"
                class="bg-[#12141c] border border-white/20 rounded-2xl w-full max-w-3xl p-6 shadow-2xl space-y-4 max-h-[90vh] flex flex-col justify-between"
            >
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <i class="ri-file-shield-line text-[#ff5b00] text-lg"></i>
                        <h3 class="text-sm font-black text-white uppercase tracking-wider truncate" x-text="previewName"></h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <a :href="previewUrl" target="_blank" download class="text-xs font-bold text-[#ff5b00] hover:text-white flex items-center gap-1">
                            <i class="ri-download-2-line"></i> Download
                        </a>
                        <button @click="showPreviewModal = false" type="button" class="text-gray-400 hover:text-white text-xl cursor-pointer">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                </div>

                <div class="flex-1 flex items-center justify-center overflow-auto p-2 min-h-[350px]">
                    <template x-if="!previewIsPdf">
                        <img :src="previewUrl" :alt="previewName" class="max-h-[60vh] max-w-full rounded-xl object-contain shadow-2xl border border-white/10">
                    </template>
                    <template x-if="previewIsPdf">
                        <iframe :src="previewUrl" class="w-full h-[60vh] rounded-xl border border-white/10"></iframe>
                    </template>
                </div>

                <div class="pt-3 border-t border-white/10 text-right">
                    <button
                        type="button"
                        @click="showPreviewModal = false"
                        class="px-5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition cursor-pointer"
                    >
                        Close Preview
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
