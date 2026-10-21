<x-app-layout>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Overdue Vaccinations</h1>
            <p class="text-sm text-slate-500">Records with due dates prior to today.</p>
        </div>
        <x-ui.button :href="route('vaccinations.index')" variant="secondary">Back to Vaccinations</x-ui.button>
    </div>

    <x-ui.card>
        @if($vaccinations->isEmpty())
            <x-ui.empty-state
                title="No overdue vaccinations."
                description="All current records are up to date."
            />
        @else
            <div class="table-shell overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Pet Name</th>
                            <th class="px-4 py-3">Vaccine</th>
                            <th class="px-4 py-3">Date Given</th>
                            <th class="px-4 py-3">Due Date</th>
                            <th class="px-4 py-3">Days Overdue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($vaccinations as $vaccination)
                            <tr class="transition-colors hover:bg-jade-50/60">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $vaccination->display_pet_name }}</td>
                                <td class="px-4 py-3">{{ $vaccination->vaccine_name }}</td>
                                <td class="px-4 py-3">{{ optional($vaccination->date_given)->format('M d, Y') }}</td>
                                <td class="px-4 py-3 font-semibold text-rose-600">{{ optional($vaccination->next_due_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    {{ $vaccination->next_due_date ? max(now()->diffInDays($vaccination->next_due_date, false) * -1, 0) : 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $vaccinations->links() }}
            </div>
        @endif
    </x-ui.card>
</x-app-layout>
