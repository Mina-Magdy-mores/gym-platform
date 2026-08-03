<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-white uppercase tracking-wider">
                <span class="neon-accent">Account</span> Profile Settings
            </h2>
            <div class="text-xs text-gray-400 font-bold uppercase tracking-widest">
                User & Trainer Profile
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Info Card -->
            <div class="p-6 sm:p-8 glass-card shadow-2xl rounded-2xl border border-white/5">
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Password Form Card -->
            <div class="p-6 sm:p-8 glass-card shadow-2xl rounded-2xl border border-white/5">
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account Card -->
            <div class="p-6 sm:p-8 glass-card shadow-2xl rounded-2xl border border-red-500/10">
                <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
