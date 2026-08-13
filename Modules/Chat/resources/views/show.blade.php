<x-app-layout>
    @php
        $otherUser = (auth()->id() === $conversationData->athlete_id) 
            ? ($conversationData->trainer ?? \App\Models\User::role('admin')->first()) 
            : $conversationData->athlete;
    @endphp

    <x-slot name="header">
        <div class="max-w-[1700px] mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('chat.index') }}" class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white transition">
                    <i class="ri-arrow-left-line text-lg"></i>
                </a>
                <h2 class="font-black text-xl text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="ri-chat-1-line text-[#ff5b00]"></i> Chat Room with <span class="neon-accent">{{ $otherUser->name ?? 'User' }}</span>
                </h2>
            </div>
            <div class="px-3.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-black uppercase tracking-widest flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Live WebSockets Connected
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="{
        conversationId: {{ $conversationData->id }},
        currentUserId: {{ auth()->id() }},
        messages: @js($conversationData->messages->map(fn($m) => [
            'id' => $m->id,
            'sender_id' => $m->sender_id,
            'sender_name' => $m->sender->name ?? 'User',
            'sender_avatar' => $m->sender->hasMedia('avatar') ? $m->sender->getFirstMediaUrl('avatar', 'thumb') : null,
            'message' => $m->message,
            'attachment_url' => $m->attachment_url,
            'created_at' => $m->created_at->format('h:i A')
        ])),
        newMessage: '',
        attachmentFile: null,
        attachmentPreview: null,
        isSending: false,

        init() {
            this.scrollToBottom();

            // WebSockets Listener via Laravel Echo
            if (window.Echo) {
                window.Echo.private('chat.' + this.conversationId)
                    .listen('.message.sent', (data) => {
                        if (data.sender_id !== this.currentUserId) {
                            this.messages.push({
                                id: data.id,
                                sender_id: data.sender_id,
                                sender_name: data.sender_name,
                                sender_avatar: data.sender_avatar,
                                message: data.message,
                                attachment_url: data.attachment_url,
                                created_at: data.created_at
                            });
                            this.playPingSound();
                            this.scrollToBottom();
                        }
                    });
            }
        },

        scrollToBottom() {
            $nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.attachmentFile = file;
                this.attachmentPreview = URL.createObjectURL(file);
            }
        },

        clearAttachment() {
            this.attachmentFile = null;
            this.attachmentPreview = null;
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },

        async sendMessage() {
            if ((!this.newMessage.trim() && !this.attachmentFile) || this.isSending) return;

            this.isSending = true;
            const formData = new FormData();
            if (this.newMessage.trim()) formData.append('message', this.newMessage.trim());
            if (this.attachmentFile) formData.append('attachment', this.attachmentFile);

            const tempMsg = this.newMessage;
            this.newMessage = '';
            const tempPreview = this.attachmentPreview;
            this.clearAttachment();

            try {
                const response = await fetch('{{ route("chat.store", $conversationData->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    this.messages.push(data.message);
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Failed to send message:', error);
            } finally {
                this.isSending = false;
            }
        },

        playPingSound() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5 note
                gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.3);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.3);
            } catch (e) {}
        }
    }">
        <div class="max-w-[1700px] mx-auto sm:px-6 lg:px-8">

            <!-- Master Chat Window Container -->
            <div class="glass-card rounded-2xl border border-white/10 overflow-hidden shadow-2xl flex flex-col h-[calc(100vh-220px)] min-h-[550px]">
                
                <!-- Chat Header Bar -->
                <div class="p-4 border-b border-white/10 bg-[#12141c]/90 backdrop-blur-md flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3.5">
                        <div class="relative">
                            @if($otherUser && $otherUser->hasMedia('avatar'))
                                <img src="{{ $otherUser->getFirstMediaUrl('avatar', 'thumb') }}" alt="{{ $otherUser->name }}" class="w-10 h-10 rounded-xl object-cover border border-[#ff5b00]/40">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-neon-gradient flex items-center justify-center text-white font-black text-sm uppercase">
                                    {{ substr($otherUser->name ?? 'U', 0, 1) }}
                                </div>
                            @endif
                            <span
                                :class="($store.presence && $store.presence.isOnline({{ $otherUser->id }})) ? 'bg-emerald-500 shadow-md shadow-emerald-500/50 animate-pulse' : 'bg-gray-500/60 opacity-50'"
                                class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-[#12141c] transition-all duration-300"
                                :title="($store.presence && $store.presence.isOnline({{ $otherUser->id }})) ? 'Online Now' : 'Offline'"
                            ></span>
                        </div>

                        <div>
                            <h3 class="text-sm font-black text-white flex items-center gap-2">
                                {{ $otherUser->name ?? 'User' }}
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    ({{ $conversationData->type === 'support' ? 'Admin Support' : 'Personal Trainer' }})
                                </span>
                            </h3>
                            <span
                                :class="($store.presence && $store.presence.isOnline({{ $otherUser->id }})) ? 'text-emerald-400' : 'text-gray-400'"
                                class="text-[10px] font-mono flex items-center gap-1 transition-colors duration-300"
                            >
                                <i :class="($store.presence && $store.presence.isOnline({{ $otherUser->id }})) ? 'ri-checkbox-circle-fill text-emerald-400' : 'ri-close-circle-line text-gray-500'"></i>
                                <span x-text="($store.presence && $store.presence.isOnline({{ $otherUser->id }})) ? 'Active Online Session' : 'Offline'"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Messages Stream Scrollable Body -->
                <div x-ref="messagesContainer" class="flex-1 p-6 overflow-y-auto space-y-3 custom-scrollbar bg-[#0b0d14]/70">
                    <template x-for="msg in messages" :key="msg.id">
                        <div :class="msg.sender_id === currentUserId ? 'justify-end' : 'justify-start'" class="flex items-end gap-2.5 my-1">
                            
                            <!-- Other User Avatar (Left Side) -->
                            <template x-if="msg.sender_id !== currentUserId">
                                <div class="w-9 h-9 rounded-full bg-neon-gradient flex items-center justify-center text-white font-black text-xs uppercase shrink-0 overflow-hidden border border-white/20 shadow-md mb-0.5 leading-none">
                                    <template x-if="msg.sender_avatar">
                                        <img :src="msg.sender_avatar" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!msg.sender_avatar">
                                        <span x-text="(msg.sender_name || 'U').replace(/[^\p{L}\p{N}]/gu, '').charAt(0) || 'U'"></span>
                                    </template>
                                </div>
                            </template>

                            <!-- Messenger Pill Chat Bubble -->
                            <div :class="msg.sender_id === currentUserId ? 'bg-neon-gradient text-white rounded-2xl rounded-tr-xs shadow-md shadow-[#ff5b00]/25' : 'bg-[#1e212d] text-gray-100 border border-white/10 rounded-2xl rounded-tl-xs'" class="max-w-[68%] px-4 py-2.5 inline-flex flex-col gap-1">
                                
                                <!-- Text Message -->
                                <template x-if="msg.message">
                                    <p class="text-xs leading-relaxed font-medium break-words whitespace-pre-wrap" x-text="msg.message"></p>
                                </template>

                                <!-- Attachment Image -->
                                <template x-if="msg.attachment_url">
                                    <div class="mt-1 rounded-xl overflow-hidden border border-white/20">
                                        <a :href="msg.attachment_url" target="_blank">
                                            <img :src="msg.attachment_url" class="max-h-60 rounded-xl object-cover hover:scale-105 transition duration-200">
                                        </a>
                                    </div>
                                </template>

                                <!-- Time Meta Stamp -->
                                <div :class="msg.sender_id === currentUserId ? 'text-white/70' : 'text-gray-400'" class="text-[9px] font-mono self-end flex items-center gap-1 mt-0.5">
                                    <span x-text="msg.created_at"></span>
                                    <template x-if="msg.sender_id === currentUserId">
                                        <i class="ri-check-double-line text-[10px] text-white"></i>
                                    </template>
                                </div>

                            </div>

                        </div>
                    </template>
                </div>

                <!-- Attachment Image Preview Box -->
                <div x-show="attachmentPreview" x-transition class="p-3 bg-[#12141c] border-t border-white/10 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img :src="attachmentPreview" class="w-12 h-12 rounded-lg object-cover border border-[#ff5b00]">
                        <span class="text-xs text-gray-300 font-bold">Image attachment ready to send</span>
                    </div>
                    <button @click="clearAttachment()" type="button" class="text-xs text-red-400 hover:text-white font-bold uppercase tracking-wider">
                        <i class="ri-close-circle-line"></i> Remove
                    </button>
                </div>

                <!-- Chat Input Action Bar -->
                <form @submit.prevent="sendMessage()" class="p-4 bg-[#12141c]/90 border-t border-white/10 flex items-center gap-3 shrink-0">
                    <!-- Image File Attachment Button -->
                    <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" accept="image/*" class="hidden">
                    <button @click="$refs.fileInput.click()" type="button" class="p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-gray-300 hover:text-[#ff5b00] transition cursor-pointer" title="Attach Image">
                        <i class="ri-image-add-line text-lg"></i>
                    </button>

                    <!-- Message Text Area -->
                    <input x-model="newMessage" @keydown.enter.prevent="sendMessage()" type="text" placeholder="Type your message here..." class="flex-1 !bg-[#181a26] border border-white/15 rounded-xl !text-white placeholder-gray-400 text-xs p-3.5 focus:border-[#ff5b00] focus:ring-1 focus:ring-[#ff5b00] focus:!bg-[#181a26] focus:!text-white transition" style="background-color: #181a26 !important; color: #ffffff !important;">

                    <!-- Send Button -->
                    <button :disabled="isSending || (!newMessage.trim() && !attachmentFile)" type="submit" class="px-5 py-3 rounded-xl bg-neon-gradient hover:opacity-90 disabled:opacity-50 text-white text-xs font-black uppercase tracking-wider transition flex items-center gap-2 cursor-pointer shadow-lg shadow-[#ff5b00]/30">
                        <span>Send</span>
                        <i class="ri-send-plane-fill text-sm"></i>
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
