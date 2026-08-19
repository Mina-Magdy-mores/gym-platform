<x-app-layout>
    <x-slot name="header">
        <div class="max-w-[1700px] mx-auto flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider flex items-center gap-3">
                <i class="ri-chat-smile-2-line text-[#ff5b00]"></i> Real-Time <span class="neon-accent">Chat & Support</span>
            </h2>
            <div class="px-4 py-1.5 rounded-full bg-neon-gradient text-white text-xs font-black uppercase tracking-widest bg-neon-glow">
                Live WebSockets Room
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        conversations: @js($conversations->map(function($c) {
            $otherUser = (auth()->id() === $c->athlete_id) 
                ? ($c->trainer ?? \App\Models\User::role('admin')->first()) 
                : $c->athlete;
            $cleanName = preg_replace('/[^\p{L}\p{N}]/u', '', $otherUser->name ?? 'User');
            return [
                'id' => $c->id,
                'other_id' => $otherUser->id,
                'other_name' => $otherUser->name ?? 'User',
                'other_initial' => strtoupper(mb_substr($cleanName, 0, 1)) ?: 'U',
                'other_avatar' => ($otherUser && $otherUser->hasMedia('avatar')) ? $otherUser->getFirstMediaUrl('avatar', 'thumb') : null,
                'type' => $c->type,
                'last_message' => $c->latestMessage?->message ?? '[Attachment Photo]',
                'last_message_sender_id' => $c->latestMessage?->sender_id,
                'last_message_time' => $c->last_message_at ? $c->last_message_at->diffForHumans() : '',
                'unread_count' => $c->unreadCountFor(auth()->id()),
            ];
        })),

        handleLiveMessage(data) {
            let index = this.conversations.findIndex(c => c.id === data.conversation_id);
            if (index !== -1) {
                let conv = this.conversations[index];
                conv.last_message = data.message || '[Attachment Photo]';
                conv.last_message_sender_id = data.sender_id;
                conv.last_message_time = 'Just Now';
                conv.unread_count++;
                
                // Move updated conversation to top of list dynamically
                this.conversations.splice(index, 1);
                this.conversations.unshift(conv);
            }
        }
    }" @chat-message-received.window="handleLiveMessage($event.detail)">

        <div class="max-w-[1700px] mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Quick Start New Conversation Contacts Carousel -->
            @if(isset($availableContacts) && $availableContacts->count() > 0)
                <div class="glass-card p-6 rounded-2xl border border-white/10 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-black text-white uppercase tracking-wide flex items-center gap-2">
                            <i class="ri-user-add-line text-emerald-400"></i> Start New Conversation With:
                        </h3>
                        <span class="text-[10px] text-gray-400 font-mono">1-Click Instant Message</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                        @foreach($availableContacts as $contact)
                            <a href="{{ route('chat.start', $contact->id) }}" class="p-3 rounded-2xl bg-white/[0.03] hover:bg-white/[0.08] border border-white/10 hover:border-[#ff5b00]/50 transition flex items-center gap-3 group shadow-sm cursor-pointer">
                                <div class="relative shrink-0 flex items-center justify-center">
                                    @if($contact->hasMedia('avatar'))
                                        <img src="{{ $contact->getFirstMediaUrl('avatar', 'thumb') }}" alt="{{ $contact->name }}" class="w-10 h-10 rounded-xl object-cover border border-white/10 group-hover:border-[#ff5b00] transition">
                                    @else
                                        @php
                                            $cleanName = preg_replace('/[^\p{L}\p{N}]/u', '', $contact->name);
                                            $initial = strtoupper(mb_substr($cleanName, 0, 1)) ?: 'U';
                                        @endphp
                                        <div class="w-10 h-10 rounded-xl bg-neon-gradient flex items-center justify-center text-white font-black text-xs uppercase shadow-md leading-none shrink-0">
                                            {{ $initial }}
                                        </div>
                                    @endif

                                    <!-- Live Dynamic Presence Indicator Dot -->
                                    <span
                                        :class="($store.presence && $store.presence.isOnline({{ $contact->id }})) ? 'bg-emerald-500 shadow-md shadow-emerald-500/50 animate-pulse' : 'bg-gray-600'"
                                        class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-[#12141c] transition-all duration-300"
                                        :title="($store.presence && $store.presence.isOnline({{ $contact->id }})) ? 'Online Now' : 'Offline'"
                                    ></span>
                                </div>
                                
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs font-black text-white group-hover:text-[#ff5b00] transition truncate">{{ $contact->name }}</h4>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block truncate">
                                        {{ $contact->getRoleNames()->first() ?? 'Member' }}
                                    </span>
                                </div>

                                <i class="ri-chat-new-line text-gray-500 group-hover:text-[#ff5b00] text-sm shrink-0 transition"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Active Conversations List Card -->
            <div class="glass-card rounded-2xl border border-white/10 overflow-hidden shadow-2xl">
                <div class="p-4 border-b border-white/10 bg-white/[0.02] flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-gray-300">
                        Active Messaging Channels (<span x-text="conversations.length"></span>)
                    </span>
                    <span class="text-[10px] font-mono text-gray-400">WebSockets Encrypted</span>
                </div>

                <div class="divide-y divide-white/5">
                    <template x-for="conv in conversations" :key="conv.id">
                        <a :href="'/chats/' + conv.id" class="p-5 flex items-center justify-between hover:bg-white/5 transition group">
                            <div class="flex items-center gap-4 min-w-0">
                                <!-- User Avatar -->
                                <div class="relative shrink-0">
                                    <template x-if="conv.other_avatar">
                                        <img :src="conv.other_avatar" :alt="conv.other_name" class="w-12 h-12 rounded-2xl object-cover border border-white/10 group-hover:border-[#ff5b00]/50 transition">
                                    </template>
                                    <template x-if="!conv.other_avatar">
                                        <div class="w-12 h-12 rounded-2xl bg-neon-gradient flex items-center justify-center text-white font-black text-base uppercase shadow-md shrink-0">
                                            <span x-text="conv.other_initial"></span>
                                        </div>
                                    </template>

                                    <!-- Live Dynamic Presence Indicator Dot -->
                                    <span
                                        :class="($store.presence && $store.presence.isOnline(conv.other_id)) ? 'bg-emerald-500 shadow-md shadow-emerald-500/50 animate-pulse' : 'bg-gray-500/60 opacity-50'"
                                        class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-[#181a24] transition-all duration-300"
                                        :title="($store.presence && $store.presence.isOnline(conv.other_id)) ? 'Online Now' : 'Offline'"
                                    ></span>
                                </div>

                                <!-- User Details & Latest Message -->
                                <div class="min-w-0 flex-1 space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-black text-white group-hover:text-[#ff5b00] transition truncate" x-text="conv.other_name"></h4>
                                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-white/5 border border-white/10 text-gray-400" x-text="conv.type === 'support' ? 'Admin Support' : 'PT Coaching'"></span>
                                        <span x-show="$store.presence && $store.presence.isOnline(conv.other_id)" class="text-[9px] font-mono text-emerald-400 font-bold px-1.5 py-0.2 rounded bg-emerald-500/10 border border-emerald-500/20">Online</span>
                                    </div>
                                    <p class="text-xs text-gray-400 truncate">
                                        <template x-if="conv.last_message_sender_id === {{ auth()->id() }}">
                                            <span class="font-bold text-gray-300">You: </span>
                                        </template>
                                        <span x-text="conv.last_message"></span>
                                    </p>
                                </div>
                            </div>

                            <!-- Right Meta & Unread Badge -->
                            <div class="flex items-center gap-4 shrink-0">
                                <div class="text-right">
                                    <span class="text-[10px] font-mono text-gray-400 block" x-text="conv.last_message_time"></span>
                                    <template x-if="conv.unread_count > 0">
                                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 text-[10px] font-black rounded-full bg-[#ff5b00] text-white shadow-lg shadow-[#ff5b00]/40 animate-pulse mt-1" x-text="conv.unread_count + ' New'"></span>
                                    </template>
                                </div>
                                <i class="ri-arrow-right-s-line text-xl text-gray-400 group-hover:text-white group-hover:translate-x-1 transition transform"></i>
                            </div>
                        </a>
                    </template>

                    <template x-if="conversations.length === 0">
                        <div class="p-12 text-center space-y-4">
                            <div class="w-16 h-16 rounded-full bg-white/5 border border-white/10 text-gray-400 flex items-center justify-center text-3xl mx-auto">
                                <i class="ri-chat-off-line"></i>
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-base font-black text-white uppercase">No Active Conversations Yet</h3>
                                <p class="text-xs text-gray-400 max-w-sm mx-auto">Select any of the available contacts above to start an instant private 1-on-1 chat session!</p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
