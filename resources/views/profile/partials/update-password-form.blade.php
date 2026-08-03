<section>
    <header class="space-y-1">
        <h2 class="text-xl font-bold text-white uppercase tracking-wider">
            <span class="neon-accent">Update</span> Password
        </h2>

        <p class="text-xs text-gray-400">
            Ensure your account is using a long, random password to stay secure.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-white font-bold" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-lg focus:border-[#ff5b00]" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" class="text-white font-bold" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-lg focus:border-[#ff5b00]" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-white font-bold" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-lg focus:border-[#ff5b00]" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-full bg-neon-gradient text-white font-bold hover:opacity-90 transition shadow-lg bg-neon-glow cursor-pointer text-sm">
                Save Password
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-400 font-bold flex items-center gap-1"
                >
                    <i class="ri-checkbox-circle-fill"></i> {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
