<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                <span class="neon-accent">Member</span> Dashboard
            </h2>
            <div class="px-4 py-1 rounded-full bg-neon-gradient text-white text-xs font-black uppercase tracking-widest bg-neon-glow">
                {{ Auth::user()->roles->first()?->name ?? 'Member' }}
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Welcome Card -->
            <div class="p-8 glass-card rounded-2xl border border-white/5 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-3 text-center md:text-left">
                    <div class="inline-block px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-gray-300">
                        Welcome Back, Champion!
                    </div>
                    <h3 class="text-3xl font-black uppercase tracking-wide">
                        Hello, <span class="neon-accent">{{ Auth::user()->name }}</span>
                    </h3>
                    <p class="text-gray-400 text-sm max-w-lg leading-relaxed">
                        Track your workouts, manage your gym subscriptions, update your trainer certificates, and control your account settings seamlessly.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-neon-gradient text-white font-bold text-sm bg-neon-glow hover:opacity-90 transition">
                            <span>Manage Profile</span> <i class="ri-user-settings-line"></i>
                        </a>
                    </div>
                </div>

                <!-- Avatar Preview Badge -->
                <div class="shrink-0">
                    <div class="relative w-32 h-32 rounded-2xl overflow-hidden glass-card border border-[#ff5b00]/30 shadow-2xl flex items-center justify-center">
                        @if(Auth::user()->getFirstMediaUrl('avatar'))
                            <img src="{{ Auth::user()->getFirstMediaUrl('avatar') }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <div class="text-4xl font-black text-white neon-accent">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
