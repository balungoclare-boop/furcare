@props([
    'modalName' => 'print-report-modal',
])

<x-modal :name="$modalName" maxWidth="lg" focusable>
    <form
        method="GET"
        action="{{ route('reports.print') }}"
        target="_blank"
        class="space-y-5 p-6"
        x-data="{ reportType: 'monthly' }"
    >
        <div>
            <h3 class="text-lg font-bold text-slate-900">Generate Printable Report</h3>
            <p class="mt-1 text-sm text-slate-500">Choose the report type and period before printing.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
                <x-input-label for="report_type" :value="__('Report Type')" />
                <select
                    id="report_type"
                    name="report_type"
                    class="mt-1 block w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                    x-model="reportType"
                    required
                >
                    <option value="monthly">Monthly</option>
                    <option value="custom">Custom Date Range</option>
                </select>
            </div>

            <div>
                <x-input-label for="start_date" :value="__('Start Date (mm/dd/yyyy)')" />
                <x-text-input
                    id="start_date"
                    name="start_date"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="mm/dd/yyyy"
                    x-bind:required="reportType === 'custom'"
                    x-bind:disabled="reportType !== 'custom'"
                />
            </div>

            <div>
                <x-input-label for="end_date" :value="__('End Date (mm/dd/yyyy)')" />
                <x-text-input
                    id="end_date"
                    name="end_date"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="mm/dd/yyyy"
                    x-bind:required="reportType === 'custom'"
                    x-bind:disabled="reportType !== 'custom'"
                />
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <button
                type="button"
                class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                x-on:click="$dispatch('close-modal', '{{ $modalName }}')"
            >
                Cancel
            </button>
            <button
                type="submit"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition duration-200 hover:scale-[1.02] hover:bg-indigo-700 hover:shadow-lg hover:shadow-jade-100"
            >
                Generate &amp; Print
            </button>
        </div>
    </form>
</x-modal>
