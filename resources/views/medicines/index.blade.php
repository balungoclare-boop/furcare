<x-app-layout>
    @php
        $schemaReady = $schemaReady ?? true;
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Medicine Inventory</h1>
            <p class="text-sm text-slate-500">Manage medicine stock levels and expiration dates.</p>
        </div>
        @if($schemaReady)
            <x-ui.button :href="route('medicines.create')">Add Medicine</x-ui.button>
        @endif
    </div>

    @if(! $schemaReady)
        <x-ui.alert type="info">
            Medicines table is not available yet. Run <code>php artisan migrate</code> to activate inventory features.
        </x-ui.alert>
    @endif

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <x-ui.stat-card label="Low Stock" :value="$lowStockCount" icon="medicine" />
        <x-ui.stat-card label="Expired" :value="$expiredCount" variant="critical" icon="alert" />
    </section>

    <x-ui.card>
        @if($medicines->isEmpty())
            <x-ui.empty-state
                title="No medicines found."
                description="Add medicines to begin inventory tracking."
            >
                @if($schemaReady)
                    <x-ui.button :href="route('medicines.create')">Create Medicine</x-ui.button>
                @endif
            </x-ui.empty-state>
        @else
            <div class="table-shell overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Stock</th>
                            <th class="px-4 py-3">Threshold</th>
                            <th class="px-4 py-3">Expiration</th>
                            <th class="px-4 py-3">Supplier</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($medicines as $medicine)
                            <tr class="transition-colors hover:bg-jade-50/60">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $medicine->name }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center gap-1">
                                        <span>{{ $medicine->stock_quantity }}</span>
                                        @if($medicine->is_low_stock)
                                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold uppercase text-amber-700">Low</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $medicine->low_stock_threshold }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center gap-1">
                                        <span>{{ optional($medicine->expiration_date)->format('M d, Y') ?? 'N/A' }}</span>
                                        @if($medicine->is_expired)
                                            <span class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold uppercase text-rose-700">Expired</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $medicine->supplier ?: 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <x-ui.button :href="route('medicines.edit', $medicine)" variant="secondary">Edit</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $medicines->links() }}</div>
        @endif
    </x-ui.card>
</x-app-layout>
