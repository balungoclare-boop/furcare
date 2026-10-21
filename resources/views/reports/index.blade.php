<x-app-layout>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Reports</h1>
            <p class="text-sm text-slate-500">Create and manage operational report summaries.</p>
        </div>
        <x-ui.button :href="route('reports.create')">Create Report</x-ui.button>
    </div>

    <x-ui.card>
        @if($reports->isEmpty())
            <x-ui.empty-state
                title="No reports found."
                description="Generate your first report to capture monthly or custom insights."
            >
                <x-ui.button :href="route('reports.create')">Create Report</x-ui.button>
            </x-ui.empty-state>
        @else
            <div class="table-shell overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Scope</th>
                            <th class="px-4 py-3">Date Range</th>
                            <th class="px-4 py-3">Created By</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($reports as $report)
                            <tr class="transition-colors hover:bg-jade-50/60">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $report->title }}</td>
                                <td class="px-4 py-3 uppercase">{{ $report->scope }}</td>
                                <td class="px-4 py-3">
                                    @if($report->starts_on || $report->ends_on)
                                        {{ optional($report->starts_on)->format('M d, Y') ?? 'N/A' }}
                                        -
                                        {{ optional($report->ends_on)->format('M d, Y') ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $report->user?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <x-ui.button :href="route('reports.show', $report)" variant="secondary">View</x-ui.button>
                                        <x-ui.button :href="route('reports.edit', $report)" variant="secondary">Edit</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $reports->links() }}
            </div>
        @endif
    </x-ui.card>
</x-app-layout>
