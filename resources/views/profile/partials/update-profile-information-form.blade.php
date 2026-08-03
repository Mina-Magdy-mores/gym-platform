<section>
    <header class="space-y-1">
        <h2 class="text-xl font-bold text-white uppercase tracking-wider">
            <span class="neon-accent">Profile</span> Information
        </h2>

        <p class="text-xs text-gray-400">
            Update your account's profile information, email address, avatar photo, and trainer certificates.
        </p>
    </header>

    <!-- Hidden Form for Avatar Deletion -->
    @if($user->getFirstMedia('avatar'))
        <form id="delete-avatar-form" method="POST" action="{{ route('profile.media.destroy', $user->getFirstMedia('avatar')->id) }}" onsubmit="return confirm('Delete profile photo?')">
            @csrf
            @method('DELETE')
        </form>
    @endif

    <!-- Hidden Forms for Certificate Deletion -->
    @if($user->getMedia('certificates')->count() > 0)
        @foreach($user->getMedia('certificates') as $cert)
            <form id="delete-cert-form-{{ $cert->id }}" method="POST" action="{{ route('profile.media.destroy', $cert->id) }}" onsubmit="return confirm('Delete this certificate?')">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    @endif

    <!-- Hidden Form for Email Verification -->
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- Main Profile Update Form -->
    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Avatar Photo Section -->
        <div class="space-y-3">
            <label class="block text-sm font-bold text-gray-300">Profile Photo (Avatar)</label>
            <div class="flex items-center gap-6">
                <div class="flex flex-col items-center gap-1">
                    @if($user->getFirstMediaUrl('avatar', 'thumb'))
                        <img src="{{ $user->getFirstMediaUrl('avatar', 'thumb') }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover border-2 border-[#ff5b00] shadow-lg">
                        <button type="submit" form="delete-avatar-form" class="text-[11px] text-red-400 hover:text-red-300 font-bold transition flex items-center gap-1 mt-1 cursor-pointer">
                            <i class="ri-delete-bin-line"></i> Remove
                        </button>
                    @else
                        <div class="w-20 h-20 rounded-full bg-neon-gradient flex items-center justify-center text-2xl font-black text-white border-2 border-white/20">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="flex-grow">
                    <input type="file" name="avatar" id="avatar" accept="image/*" class="block w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-white/10 file:text-white hover:file:bg-[#ff5b00] hover:file:text-white transition cursor-pointer">
                    <p class="text-[10px] text-gray-400 mt-1">Allowed formats: JPG, PNG, WEBP (Max 2MB)</p>
                </div>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('avatar')" />
        </div>

        <!-- Name Input -->
        <div>
            <x-input-label for="name" :value="__('Name')" class="text-white font-bold" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-lg focus:border-[#ff5b00]" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email Input -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-white font-bold" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-lg focus:border-[#ff5b00]" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-300">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-[#ff5b00] hover:text-white rounded-md focus:outline-none">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Trainer Certificates Section -->
        <div class="space-y-3 pt-4 border-t border-white/5">
            <label class="block text-sm font-bold text-gray-300">Trainer Certificates & Documents</label>

            <!-- Display existing certificates if any -->
            @if($user->getMedia('certificates')->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                    @foreach($user->getMedia('certificates') as $cert)
                        <div class="flex items-center justify-between p-3 rounded-lg glass-card text-xs">
                            <div class="flex items-center gap-2 truncate">
                                <i class="ri-file-pdf-fill text-red-500 text-lg"></i>
                                <span class="truncate text-gray-200">{{ $cert->file_name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ $cert->getUrl() }}" target="_blank" class="px-2.5 py-1 rounded-md bg-white/10 hover:bg-[#ff5b00] text-white font-bold transition">
                                    View
                                </a>
                                <button type="submit" form="delete-cert-form-{{ $cert->id }}" class="p-1 rounded bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white transition cursor-pointer" title="Delete Certificate">
                                    <i class="ri-delete-bin-line text-sm"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <input type="file" name="certificates[]" id="certificates" multiple accept=".pdf,image/*" class="block w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-white/10 file:text-white hover:file:bg-[#ff5b00] hover:file:text-white transition cursor-pointer">
            <p class="text-[10px] text-gray-400 mt-1">Upload PDF or Images (Multiple files allowed, Max 5MB each)</p>
            <x-input-error class="mt-1" :messages="$errors->get('certificates')" />
        </div>

        <!-- Submit Button -->
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-full bg-neon-gradient text-white font-bold hover:opacity-90 transition shadow-lg bg-neon-glow cursor-pointer text-sm">
                Save Changes
            </button>

            @if (session('status') === 'profile-updated' || session('status') === 'media-deleted')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-400 font-bold flex items-center gap-1"
                >
                    <i class="ri-checkbox-circle-fill"></i> {{ session('status') === 'media-deleted' ? __('Media deleted successfully.') : __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
