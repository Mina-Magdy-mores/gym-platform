

import Alpine from 'alpinejs';
import ScrollReveal from 'scrollreveal';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Clean WebSockets Driver Setup
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    forceTLS: true,
    enabledTransports: ['ws', 'wss'],
});

window.Alpine = Alpine;
window.ScrollReveal = ScrollReveal;

document.addEventListener('alpine:init', () => {
    Alpine.store('unreadChat', {
        count: 0,
        setCount(val) { this.count = val; },
        increment() { this.count++; },
        decrement(val = 1) { this.count = Math.max(0, this.count - val); }
    });

    Alpine.store('unreadNotifications', {
        count: 0,
        setCount(val) { this.count = val; },
        increment() { this.count++; },
        markAllRead() { this.count = 0; }
    });

    Alpine.store('presence', {
        onlineIds: [],
        setOnline(users) {
            this.onlineIds = users.map(u => Number(u.id));
        },
        add(user) {
            const id = Number(user.id);
            if (!this.onlineIds.includes(id)) {
                this.onlineIds.push(id);
            }
        },
        remove(user) {
            const id = Number(user.id);
            this.onlineIds = this.onlineIds.filter(i => i !== id);
        },
        isOnline(userId) {
            return this.onlineIds.includes(Number(userId));
        }
    });

    // ─── Initialize unread chat count from server (injected via meta tag) ───
    const metaUnread = document.querySelector('meta[name="unread-chat-count"]');
    if (metaUnread) {
        Alpine.store('unreadChat').setCount(parseInt(metaUnread.content) || 0);
    }

    // ─── Initialize unread notifications count from server (injected via meta tag) ───
    const metaNotif = document.querySelector('meta[name="unread-notification-count"]');
    if (metaNotif) {
        Alpine.store('unreadNotifications').setCount(parseInt(metaNotif.content) || 0);
    }

    // ─── Real-Time Chat Bell: Listen on the user's private channel ───
    const metaUserId = document.querySelector('meta[name="auth-user-id"]');
    if (metaUserId && window.Echo) {
        const userId = metaUserId.content;
        window.Echo.private(`user.${userId}`)

            // ── Chat: new message received ──
            .listen('.message.sent', (data) => {
                const currentPath = window.location.pathname;
                const isInRoom   = currentPath === `/chats/${data.conversation_id}`;
                if (!isInRoom) {
                    Alpine.store('unreadChat').increment();
                }
                window.dispatchEvent(new CustomEvent('chat-message-received', {
                    detail: data
                }));
            })

            // ── Notifications: new notification received ──
            .listen('.notification.sent', (data) => {
                Alpine.store('unreadNotifications').increment();

                // Fire a toast event any page can catch and display
                window.dispatchEvent(new CustomEvent('notification-received', {
                    detail: data
                }));
            });
    }
});

Alpine.start();
