<x-app-layout>
    @php
        $schemaReady = $schemaReady ?? true;
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Hospitalization Monitoring</h1>
            <p class="text-sm text-slate-500">Track active confinements, discharge details, and progress updates.</p>
        </div>
        @if($schemaReady)
            <x-ui.button :href="route('hospitalizations.create')">Add Hospitalization</x-ui.button>
        @endif
    </div>

    @if(! $schemaReady)
        <x-ui.alert type="info">
            Hospitalizations table is not available yet. Run <code>php artisan migrate</code> to activate confinement monitoring.
        </x-ui.alert>
    @endif

    <x-ui.card title="Active Confined Pets">
        @if($activeHospitalizations->isEmpty())
            <x-ui.empty-state
                title="No active confinements."
                description="Active hospitalization records will appear here."
            />
        @else
            <div class="table-shell overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Pet</th>
                            <th class="px-4 py-3">Owner</th>
                            <th class="px-4 py-3">Admitted</th>
                            <th class="px-4 py-3">Medication Schedule</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($activeHospitalizations as $hospitalization)
                            <tr class="transition-colors hover:bg-jade-50/60">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $hospitalization->pet?->pet_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">{{ $hospitalization->pet?->owner_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">{{ optional($hospitalization->admitted_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $hospitalization->medication_schedule ?: 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <x-ui.button :href="route('hospitalizations.show', $hospitalization)" variant="secondary">View</x-ui.button>
                                        <x-ui.button :href="route('hospitalizations.edit', $hospitalization)" variant="secondary">Edit</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $activeHospitalizations->links() }}</div>
        @endif
    </x-ui.card>

    <x-ui.card title="Discharged Pets">
        @if($dischargedHospitalizations->isEmpty())
            <x-ui.empty-state
                title="No discharged records."
                description="Completed confinement records will appear here."
            />
        @else
            <div class="table-shell overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Pet</th>
                            <th class="px-4 py-3">Admitted</th>
                            <th class="px-4 py-3">Discharged</th>
                            <th class="px-4 py-3">Summary</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($dischargedHospitalizations as $hospitalization)
                            <tr class="transition-colors hover:bg-jade-50/60">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $hospitalization->pet?->pet_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">{{ optional($hospitalization->admitted_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ optional($hospitalization->discharge_date)->format('M d, Y') ?: 'N/A' }}</td>
                                <td class="px-4 py-3">{{ $hospitalization->discharge_summary ?: 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <x-ui.button :href="route('hospitalizations.show', $hospitalization)" variant="secondary">View</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $dischargedHospitalizations->links() }}</div>
        @endif
    </x-ui.card>
</x-app-layout>
