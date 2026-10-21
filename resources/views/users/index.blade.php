<x-app-layout>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">User Management</h1>
            <p class="text-sm text-slate-500">Manage Admin, Veterinary Staff, and Client accounts.</p>
        </div>
        <x-ui.button :href="route('admin.users.create')">Add User</x-ui.button>
    </div>

    <x-ui.card>
        @if($users->isEmpty())
            <x-ui.empty-state
                title="No users available."
                description="Add your first administrator account."
            >
                <x-ui.button :href="route('admin.users.create')">Create User</x-ui.button>
            </x-ui.empty-state>
        @else
            <div class="table-shell overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Active</th>
                            <th class="px-4 py-3">Pets</th>
                            <th class="px-4 py-3">Password Reset Required</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($users as $user)
                            <tr class="transition-colors hover:bg-jade-50/60">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $user->name }}</td>
                                <td class="px-4 py-3">{{ $user->email }}</td>
                                <td class="px-4 py-3 uppercase">{{ str_replace('_', ' ', $user->role) }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $user->pets_count }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $user->must_change_password ? 'bg-jade-100 text-jade-700' : 'bg-indigo-100 text-indigo-700' }}">
                                        {{ $user->must_change_password ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <x-ui.button :href="route('admin.users.edit', $user)" variant="secondary">Edit</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </x-ui.card>
</x-app-layout>
