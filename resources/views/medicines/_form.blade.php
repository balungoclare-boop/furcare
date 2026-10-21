@props([
    'medicine' => null,
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
            <x-input-label for="name" :value="__('Medicine Name')" />
            <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $medicine?->name)" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="supplier" :value="__('Supplier')" />
            <x-text-input id="supplier" name="supplier" class="mt-1 block w-full" :value="old('supplier', $medicine?->supplier)" />
            <x-input-error :messages="$errors->get('supplier')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="stock_quantity" :value="__('Stock Quantity')" />
            <x-text-input id="stock_quantity" name="stock_quantity" type="number" min="0" class="mt-1 block w-full" :value="old('stock_quantity', $medicine?->stock_quantity ?? 0)" required />
            <x-input-error :messages="$errors->get('stock_quantity')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="stock_adjustment_reason" :value="__('Stock Adjustment Reason')" />
            <textarea id="stock_adjustment_reason" name="stock_adjustment_reason" rows="2" maxlength="500" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('stock_adjustment_reason') }}</textarea>
            <p class="mt-1 text-xs text-slate-500">Required for opening stock above zero and whenever the stock quantity changes.</p>
            <x-input-error :messages="$errors->get('stock_adjustment_reason')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="low_stock_threshold" :value="__('Low Stock Threshold')" />
            <x-text-input id="low_stock_threshold" name="low_stock_threshold" type="number" min="0" class="mt-1 block w-full" :value="old('low_stock_threshold', $medicine?->low_stock_threshold ?? 10)" required />
            <x-input-error :messages="$errors->get('low_stock_threshold')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="expiration_date" :value="__('Expiration Date')" />
            <x-text-input id="expiration_date" name="expiration_date" type="date" class="mt-1 block w-full" :value="old('expiration_date', optional($medicine?->expiration_date)->toDateString())" />
            <x-input-error :messages="$errors->get('expiration_date')" class="mt-2" />
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <x-ui.button type="submit">Save</x-ui.button>
        <x-ui.button :href="route('medicines.index')" variant="secondary">Cancel</x-ui.button>
    </div>
</form>
