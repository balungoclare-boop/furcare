<x-app-layout>
    @php
        $schemaReady = $schemaReady ?? true;
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Announcements</h1>
            <p class="text-sm text-slate-500">Manage clinic announcements shown on the welcome page.</p>
        </div>
        @if($schemaReady)
            <x-ui.button :href="route('announcements.create')">Create Announcement</x-ui.button>
        @endif
    </div>

    @if(! $schemaReady)
        <x-ui.alert type="info">
            Announcements table is not available yet. Run <code>php artisan migrate</code> to activate this module.
        </x-ui.alert>
    @endif

    <x-ui.card>
        @if($schemaReady && $announcements->isEmpty())
            <x-ui.empty-state
                title="No announcements yet."
                description="Create your first announcement to display clinic updates on the welcome page."
            >
                <x-ui.button :href="route('announcements.create')">Post Announcement</x-ui.button>
            </x-ui.empty-state>
        @elseif($schemaReady)
            <div class="table-shell overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Posted By</th>
                            <th class="px-4 py-3">Schedule</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($announcements as $announcement)
                            <tr class="transition-colors hover:bg-jade-50/60">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-800">{{ $announcement->title }}</p>
                                    <div class="mt-1 flex flex-wrap items-center gap-1">
                                        @if($announcement->is_pinned)
                                            <span class="rounded-full bg-indigo-600 px-2 py-0.5 text-[10px] font-semibold uppercase text-white">PINNED</span>
                                        @endif
                                        @if($announcement->priority === 'important')
                                            <span class="rounded-full bg-rose-600 px-2 py-0.5 text-[10px] font-semibold uppercase text-white">IMPORTANT</span>
                                        @else
                                            <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-semibold uppercase text-slate-700">NORMAL</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 max-w-xl text-xs text-slate-500 [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:2] overflow-hidden">
                                        {{ $announcement->description }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-700">{{ $announcement->creator?->name ?? 'N/A' }}</p>
                                    <p class="text-xs uppercase text-slate-500">{{ str_replace('_', ' ', $announcement->role) }}</p>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-600">
                                    <p><span class="font-semibold">Published:</span> {{ optional($announcement->publish_at)->format('M d, Y h:i A') ?? 'Immediate' }}</p>
                                    <p class="mt-1"><span class="font-semibold">Expires:</span> {{ optional($announcement->expires_at)->format('M d, Y h:i A') ?? 'No expiry' }}</p>
                                    <p class="mt-1"><span class="font-semibold">Created:</span> {{ optional($announcement->created_at)->format('M d, Y h:i A') }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <x-ui.button :href="route('announcements.edit', $announcement)" variant="secondary">Edit</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $announcements->links() }}</div>
        @endif
    </x-ui.card>
</x-app-layout>
