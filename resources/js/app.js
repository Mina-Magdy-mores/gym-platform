

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
});

Alpine.start();
