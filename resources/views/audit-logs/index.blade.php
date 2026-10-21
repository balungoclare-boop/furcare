<x-app-layout>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Audit Trail</h1>
            <p class="text-sm text-slate-500">Filter and review critical system changes.</p>
        </div>
    </div>

    <x-ui.card>
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-6">
            <div>
                <x-input-label for="event_type" :value="__('Event')" />
                <select id="event_type" name="event_type" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                    <option value="">All</option>
                    @foreach($eventTypes as $eventType)
                        <option value="{{ $eventType }}" @selected(request('event_type') === $eventType)>{{ $eventType }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="entity_type" :value="__('Entity')" />
                <select id="entity_type" name="entity_type" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                    <option value="">All</option>
                    @foreach($entityTypes as $entityType)
                        <option value="{{ $entityType }}" @selected(request('entity_type') === $entityType)>{{ $entityType }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="user_id" :value="__('User ID')" />
                <x-text-input id="user_id" name="user_id" type="number" min="1" class="mt-1 block w-full" :value="request('user_id')" />
            </div>

            <div>
                <x-input-label for="date_from" :value="__('Date From')" />
                <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full" :value="request('date_from')" />
            </div>

            <div>
                <x-input-label for="date_to" :value="__('Date To')" />
                <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full" :value="request('date_to')" />
            </div>

            <div>
                <x-input-label for="search" :value="__('Search')" />
                <x-text-input id="search" name="search" class="mt-1 block w-full" :value="request('search')" placeholder="Description, event..." />
            </div>

            <div class="md:col-span-5 flex flex-wrap items-center gap-2">
                <x-ui.button type="submit">Apply Filters</x-ui.button>
                <x-ui.button :href="route('admin.audit-logs.index')" variant="secondary">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card>
        @if($auditLogs->isEmpty())
            <x-ui.empty-state
                title="No audit entries found."
                description="Critical updates and deletions will appear here."
            />
        @else
            <div class="table-shell overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Timestamp</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Event</th>
                            <th class="px-4 py-3">Entity</th>
                            <th class="px-4 py-3">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($auditLogs as $log)
                            <tr class="transition-colors hover:bg-jade-50/60">
                                <td class="px-4 py-3">{{ optional($log->created_at)->format('M d, Y h:i A') }}</td>
                                <td class="px-4 py-3">{{ $log->user?->name ?? 'System' }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold uppercase text-indigo-700">
                                        {{ $log->event_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $log->entity_type }}{{ $log->entity_id ? ' #'.$log->entity_id : '' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $log->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(method_exists($auditLogs, 'links'))
                <div class="mt-4">{{ $auditLogs->links() }}</div>
            @endif
        @endif
    </x-ui.card>
</x-app-layout>
