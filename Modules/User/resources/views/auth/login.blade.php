<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div x-data="{
        email: '{{ old('email') }}',
        password: '',
        fill(e, p) {
            this.email = e;
            this.password = p;
        }
    }" class="space-y-6">

        <!-- ⚡ ONE-CLICK DEMO ACCOUNTS SHOWCASE -->
        <div class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/10 relative overflow-hidden backdrop-blur-md">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                    <i class="ri-flashlight-fill text-[#ff5b00]"></i>
                    <span>Quick Demo Accounts (1-Click Fill)</span>
                </span>
                <span class="text-[9px] font-bold text-gray-500 bg-white/5 px-2 py-0.5 rounded-full">Test Sandbox</span>
            </div>
            
            <div class="grid grid-cols-3 gap-2">
                <!-- Admin Demo Button -->
                <button 
                    type="button" 
                    @click="fill('mina@gym.com', 'password123')"
                    class="p-2 rounded-xl bg-[#12141c] hover:bg-neon-gradient border border-white/5 hover:border-transparent text-left transition group shadow-sm cursor-pointer"
                >
                    <div class="flex items-center gap-1.5 text-xs font-black text-white group-hover:text-white">
                        <span>👑</span>
                        <span class="truncate">Admin</span>
                    </div>
                    <p class="text-[9px] text-gray-400 group-hover:text-white/80 font-mono truncate mt-0.5">mina@gym.com</p>
                </button>

                <!-- Trainer Demo Button -->
                <button 
                    type="button" 
                    @click="fill('ahmed.trainer@fitclub.com', 'password123')"
                    class="p-2 rounded-xl bg-[#12141c] hover:bg-neon-gradient border border-white/5 hover:border-transparent text-left transition group shadow-sm cursor-pointer"
                >
                    <div class="flex items-center gap-1.5 text-xs font-black text-white group-hover:text-white">
                        <span>🏋️</span>
                        <span class="truncate">Coach</span>
                    </div>
                    <p class="text-[9px] text-gray-400 group-hover:text-white/80 font-mono truncate mt-0.5">ahmed.trainer@...</p>
                </button>

                <!-- Member Demo Button -->
                <button 
                    type="button" 
                    @click="fill('member@fitclub.com', 'password123')"
                    class="p-2 rounded-xl bg-[#12141c] hover:bg-neon-gradient border border-white/5 hover:border-transparent text-left transition group shadow-sm cursor-pointer"
                >
                    <div class="flex items-center gap-1.5 text-xs font-black text-white group-hover:text-white">
                        <span>👤</span>
                        <span class="truncate">Member</span>
                    </div>
                    <p class="text-[9px] text-gray-400 group-hover:text-white/80 font-mono truncate mt-0.5">member@fitclub...</p>
                </button>
            </div>
        </div>

        <!-- LOGIN FORM -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email Address')" class="text-xs font-bold text-gray-300 uppercase tracking-wider" />
                <x-text-input 
                    id="email" 
                    x-model="email"
                    class="block mt-1 w-full bg-[#12141c] border-white/10 text-white focus:border-[#ff5b00] focus:ring-[#ff5b00] rounded-xl text-sm" 
                    type="email" 
                    name="email" 
                    required 
                    autofocus 
                    autocomplete="username" 
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-rose-400" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-xs font-bold text-gray-300 uppercase tracking-wider" />
                <x-text-input 
                    id="password" 
                    x-model="password"
                    class="block mt-1 w-full bg-[#12141c] border-white/10 text-white focus:border-[#ff5b00] focus:ring-[#ff5b00] rounded-xl text-sm"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password" 
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-rose-400" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between text-xs pt-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded bg-[#12141c] border-white/10 text-[#ff5b00] shadow-sm focus:ring-[#ff5b00]" name="remember">
                    <span class="ms-2 text-gray-400 hover:text-gray-300">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-gray-400 hover:text-[#ff5b00] transition" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button 
                    type="submit" 
                    class="w-full bg-neon-gradient hover:opacity-90 text-white font-black py-3 px-4 rounded-xl text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-[#ff5b00]/25 transition cursor-pointer"
                >
                    <i class="ri-login-box-line text-base"></i>
                    <span>{{ __('Log in to Account') }}</span>
                </button>
            </div>

            <!-- Register Link -->
            <div class="text-center pt-2 border-t border-white/5">
                <p class="text-xs text-gray-400">
                    Don't have an account yet? 
                    <a href="{{ route('register') }}" class="text-[#ff5b00] font-bold hover:underline ml-1">Create Account</a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
