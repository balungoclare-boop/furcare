<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Enter the 6-digit verification code we sent to your email.') }}
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form id="verify-reset-code-form" method="POST" action="{{ route('password.verify') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                          :value="old('email', $email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="code" :value="__('Verification Code')" />
            <x-text-input id="code" class="block mt-1 w-full tracking-widest text-center"
                          type="text" name="code" inputmode="numeric" pattern="\d{6}" maxlength="6" required />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

    </form>

    <div class="flex items-center justify-between mt-4">
        <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <input type="hidden" name="email" value="{{ old('email', $email) }}">
                <button type="submit" class="text-sm font-semibold text-indigo-700 hover:text-indigo-800 focus:outline-none">
                    {{ __('Resend Code') }}
                </button>
        </form>

        <x-primary-button type="submit" form="verify-reset-code-form">
            {{ __('Verify Code') }}
        </x-primary-button>
    </div>
</x-guest-layout>
