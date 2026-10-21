<x-app-layout>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $medicine->name }}</h1>
            <p class="text-sm text-slate-500">Inventory details and stock status.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.button :href="route('medicines.edit', $medicine)" variant="secondary">Edit</x-ui.button>
            <x-ui.button :href="route('medicines.index')" variant="secondary">Back</x-ui.button>
        </div>
    </div>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Stock Quantity" :value="$medicine->stock_quantity" icon="medicine" />
        <x-ui.stat-card label="Low Stock Threshold" :value="$medicine->low_stock_threshold" />
        <x-ui.stat-card label="Expiration" :value="optional($medicine->expiration_date)->format('M d, Y') ?: 'N/A'" icon="clock" />
    </section>

    <x-ui.card title="Medicine Information">
        <div class="space-y-3 text-sm text-slate-700">
            <p><span class="font-semibold text-slate-900">Supplier:</span> {{ $medicine->supplier ?: 'N/A' }}</p>
            <p>
                <span class="font-semibold text-slate-900">Status:</span>
                @if($medicine->is_expired)
                    <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold uppercase text-rose-700">Expired</span>
                @elseif($medicine->is_low_stock)
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase text-amber-700">Low Stock</span>
                @else
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase text-emerald-700">In Stock</span>
                @endif
            </p>
        </div>
    </x-ui.card>
</x-app-layout>
