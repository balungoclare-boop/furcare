@props([
    'user' => null,
    'action',
    'method' => 'POST',
])

<form method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if(!in_array($method, ['POST', 'GET'], true))
        @method($method)
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $user?->name)" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user?->email)" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="role" :value="__('Role')" />
            <select id="role" name="role" class="mt-1 block w-full rounded-md border-slate-300">
                @foreach([\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_VETERINARY_STAFF, \App\Models\User::ROLE_CLIENT] as $role)
                    <option value="{{ $role }}" @selected(old('role', $user?->role ?? \App\Models\User::ROLE_CLIENT) === $role)>
                        {{ strtoupper(str_replace('_', ' ', $role)) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
            <label class="inline-flex flex-wrap items-center gap-2 text-sm text-slate-700">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    class="rounded border-indigo-200 text-indigo-600 focus:ring-indigo-300"
                    @checked(old('is_active', $user?->is_active ?? true))
                >
                User account is active
            </label>
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
            <label class="inline-flex flex-wrap items-center gap-2 text-sm text-slate-700">
                <input
                    type="checkbox"
                    name="must_change_password"
                    value="1"
                    class="rounded border-indigo-200 text-indigo-600 focus:ring-indigo-300"
                    @checked(old('must_change_password', $user?->must_change_password ?? true))
                >
                Force password change on next login
            </label>
        </div>

        <div>
            <x-input-label for="password" :value="$user ? __('New Password (optional)') : __('Password')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" :required="!$user" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" :required="!$user" />
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <x-ui.button type="submit">Save</x-ui.button>
        <x-ui.button :href="route('admin.users.index')" variant="secondary">Cancel</x-ui.button>
    </div>
</form>
