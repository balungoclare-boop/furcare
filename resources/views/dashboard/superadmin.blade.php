<x-app-layout>
    <section class="rounded-3xl bg-gradient-to-r from-indigo-700 via-indigo-600 to-jade-500 p-6 text-white shadow-xl animate-rise">
        <p class="text-xs uppercase tracking-[0.25em] text-white">Admin Dashboard</p>
        <h1 class="mt-2 text-3xl font-extrabold text-white">System Analytics</h1>
        <p class="mt-2 text-sm text-white">Global view across users, patients, appointments, and vaccines.</p>
    </section>

    <section class="space-y-3">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">System Overview</p>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <x-ui.stat-card label="Clients" :value="$metrics['totalClients']" icon="users" />
            <x-ui.stat-card label="Pets" :value="$metrics['totalPets']" icon="pets" />
            <x-ui.stat-card label="Appointments" :value="$metrics['totalAppointments']" icon="calendar" />
            <x-ui.stat-card label="Vaccinations" :value="$metrics['totalVaccinations']" icon="syringe" />
            <x-ui.stat-card label="Due Soon" :value="$metrics['dueSoonVaccinations']" icon="clock" />
            <x-ui.stat-card label="Overdue" :value="$metrics['overdueVaccinations']" variant="critical" icon="alert" />
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-5">
        <div class="space-y-4 lg:col-span-3">
            <x-ui.card title="Vaccination Overview" subtitle="Vaccination appointment analytics">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-indigo-600">Total Vaccination Appointments</p>
                        <p class="mt-2 text-2xl font-bold text-indigo-700">{{ $vaccinationAnalytics['totalAppointments'] }}</p>
                    </div>
                    <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-indigo-600">Most Common Vaccine</p>
                        <p class="mt-2 text-sm font-semibold text-slate-700">{{ $vaccinationAnalytics['mostCommonVaccine'] }}</p>
                    </div>
                    <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-indigo-600">Vaccination Count This Month</p>
                        <p class="mt-2 text-2xl font-bold text-indigo-700">{{ $vaccinationAnalytics['thisMonthCount'] }}</p>
                    </div>
                </div>
                <div class="mt-4 border-t border-indigo-100 pt-4">
                    <canvas id="superadminVaccinationChart" height="110"></canvas>
                </div>
            </x-ui.card>

            <x-ui.card title="Checkup Overview" subtitle="Checkup appointment analytics">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div class="rounded-xl border border-jade-100 bg-jade-50 p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-jade-600">Total Checkup Appointments</p>
                        <p class="mt-2 text-2xl font-bold text-jade-600">{{ $checkupAnalytics['totalAppointments'] }}</p>
                    </div>
                    <div class="rounded-xl border border-jade-100 bg-jade-50 p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-jade-600">Most Common Checkup Reason</p>
                        <p class="mt-2 text-sm font-semibold text-slate-700">{{ $checkupAnalytics['mostCommonReason'] }}</p>
                    </div>
                    <div class="rounded-xl border border-jade-100 bg-jade-50 p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-jade-600">Checkups This Month</p>
                        <p class="mt-2 text-2xl font-bold text-jade-600">{{ $checkupAnalytics['thisMonthCount'] }}</p>
                    </div>
                </div>
                <div class="mt-4 border-t border-jade-100 pt-4">
                    <canvas id="superadminCheckupChart" height="110"></canvas>
                </div>
            </x-ui.card>
        </div>

        <div class="space-y-4 lg:col-span-2">
            <x-ui.card title="Quick Access">
                <div class="grid grid-cols-1 gap-3">
                    <x-ui.button :href="route('admin.users.index')">Manage Users</x-ui.button>
                    <x-ui.button :href="route('appointments.index')" variant="secondary">Open Appointments</x-ui.button>
                    <x-ui.button :href="route('hospitalizations.index')" variant="secondary">Hospitalizations</x-ui.button>
                    <x-ui.button :href="route('medicines.index')" variant="secondary">Medicines</x-ui.button>
                </div>
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Vaccination Reminders</p>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase text-amber-700">
                            Due Soon: {{ $metrics['dueSoonVaccinations'] }}
                        </span>
                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold uppercase text-rose-700">
                            Overdue: {{ $metrics['overdueVaccinations'] }}
                        </span>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-base font-bold text-slate-900">Reports</h3>
                    <button
                        type="button"
                        class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition duration-200 hover:scale-[1.02] hover:bg-indigo-700 hover:shadow-lg hover:shadow-jade-100"
                        x-on:click="$dispatch('open-modal', 'print-report-modal')"
                    >
                        Print Report
                    </button>
                </div>
                <p class="mt-2 text-sm text-slate-500">Generate monthly or custom clinic performance reports.</p>
                <div class="mt-4">
                    <x-ui.button :href="route('reports.index')" variant="secondary">Open Reports</x-ui.button>
                </div>
            </x-ui.card>
        </div>
    </section>

    <section>
        <x-ui.card title="Recent Activity" subtitle="Latest system actions from staff and client activity.">
            @if($recentActivities->isEmpty())
                <x-ui.empty-state
                    title="No recent activity yet."
                    description="Approval and update logs will appear here."
                />
            @else
                <div class="space-y-3">
                    @foreach($recentActivities as $activity)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-slate-800">
                                    {{ $activity->user?->name ?? 'System' }} · {{ $activity->action }}
                                </p>
                                <span class="text-xs uppercase tracking-[0.1em] text-slate-500">
                                    {{ optional($activity->created_at)->format('M d, Y h:i A') }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-slate-600">{{ $activity->description }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </section>

    <section>
        <x-ui.card title="Staff Performance" subtitle="Operational metrics per veterinary staff member.">
            @if($staffPerformance->isEmpty())
                <x-ui.empty-state
                    title="No active staff found."
                    description="Staff performance will appear once accounts are available."
                />
            @else
                <div class="table-shell overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Staff</th>
                                <th class="px-4 py-3">Consultations</th>
                                <th class="px-4 py-3">Vaccinations</th>
                                <th class="px-4 py-3">Grooming</th>
                                <th class="px-4 py-3">Completed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($staffPerformance as $performance)
                                <tr class="transition-colors hover:bg-jade-50/60">
                                    <td class="px-4 py-3 font-semibold text-slate-700">{{ $performance['staff_name'] }}</td>
                                    <td class="px-4 py-3">{{ $performance['total_consultations'] }}</td>
                                    <td class="px-4 py-3">{{ $performance['vaccinations_administered'] }}</td>
                                    <td class="px-4 py-3">{{ $performance['grooming_handled'] }}</td>
                                    <td class="px-4 py-3">{{ $performance['completed_appointments'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.card>
    </section>

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-ui.card title="Active Hospitalizations">
            @if($activeHospitalizationsList->isEmpty())
                <x-ui.empty-state
                    title="No active confinement records."
                    description="Confinement updates will appear here."
                />
            @else
                <div class="space-y-2">
                    @foreach($activeHospitalizationsList as $hospitalization)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-sm font-semibold text-slate-800">{{ $hospitalization->pet?->pet_name ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-500">
                                {{ $hospitalization->pet?->owner_name ?? 'N/A' }} · Admitted {{ optional($hospitalization->admitted_date)->format('M d, Y') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="mt-4">
                <x-ui.button :href="route('hospitalizations.index')" variant="secondary">Open Hospitalizations</x-ui.button>
            </div>
        </x-ui.card>

        <x-ui.card title="Medicine Alerts">
            @if($lowStockMedicinesList->isEmpty())
                <x-ui.empty-state
                    title="No low-stock medicines."
                    description="Inventory alerts will appear when stock reaches threshold."
                />
            @else
                <div class="space-y-2">
                    @foreach($lowStockMedicinesList as $medicine)
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2">
                            <p class="text-sm font-semibold text-slate-800">{{ $medicine->name }}</p>
                            <p class="text-xs text-amber-700">
                                Stock {{ $medicine->stock_quantity }} / Threshold {{ $medicine->low_stock_threshold }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.1em]">
                <span class="rounded-full bg-amber-100 px-3 py-1 font-semibold text-amber-700">Low Stock: {{ $metrics['lowStockMedicines'] }}</span>
                <span class="rounded-full bg-rose-100 px-3 py-1 font-semibold text-rose-700">Expired: {{ $metrics['expiredMedicines'] }}</span>
            </div>
            <div class="mt-3">
                <x-ui.button :href="route('medicines.index')" variant="secondary">Open Medicines</x-ui.button>
            </div>
        </x-ui.card>
    </section>

    <section class="space-y-5">
        <x-ui.card>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-bold text-indigo-700">Vaccination Appointments ({{ $vaccinationAppointments->total() }})</h2>
                <x-ui.button :href="route('appointments.index')" variant="secondary">Manage All</x-ui.button>
            </div>
            @if($vaccinationAppointments->isEmpty())
                <x-ui.empty-state
                    title="No vaccination appointments."
                    description="Vaccination requests will appear here once submitted."
                />
            @else
                <div class="mt-4 table-shell overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Pet Name</th>
                                <th class="px-4 py-3">Owner</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Time</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($vaccinationAppointments as $appointment)
                                <tr class="transition-colors hover:bg-jade-50/60">
                                    <td class="px-4 py-3 font-semibold text-slate-700">{{ $appointment->display_pet_name }}</td>
                                    <td class="px-4 py-3">{{ $appointment->pet?->owner_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ optional($appointment->appointment_date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">{{ $appointment->preferred_time ? substr((string) $appointment->preferred_time, 0, 5) : 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-col items-start gap-1">
                                            @if($appointment->is_emergency)
                                                <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold uppercase text-rose-700">
                                                    Emergency
                                                </span>
                                            @endif
                                            <x-ui.appointment-status-badge :appointment="$appointment" />
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            <x-ui.appointment-status-menu :appointment="$appointment" />
                                            <x-ui.button :href="route('appointments.edit', $appointment)" variant="secondary">Reschedule</x-ui.button>
                                            @if(in_array($appointment->status, ['pending', 'approved'], true))
                                                <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" x-on:submit.prevent="$dispatch('confirm-form', { form: $el, message: 'Cancel this appointment?' })">
                                                    @csrf
                                                    <x-ui.button variant="danger">Cancel</x-ui.button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $vaccinationAppointments->links() }}</div>
            @endif
        </x-ui.card>

        <div class="border-t border-jade-100"></div>

        <x-ui.card>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-bold text-indigo-700">Grooming Appointments ({{ $groomingAppointments->total() }})</h2>
                <x-ui.button :href="route('appointments.index')" variant="secondary">Manage All</x-ui.button>
            </div>
            @if($groomingAppointments->isEmpty())
                <x-ui.empty-state
                    title="No grooming appointments."
                    description="Grooming requests will appear here once submitted."
                />
            @else
                <div class="mt-4 table-shell overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Pet Name</th>
                                <th class="px-4 py-3">Owner</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Time</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($groomingAppointments as $appointment)
                                <tr class="transition-colors hover:bg-jade-50/60">
                                    <td class="px-4 py-3 font-semibold text-slate-700">{{ $appointment->display_pet_name }}</td>
                                    <td class="px-4 py-3">{{ $appointment->pet?->owner_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ optional($appointment->appointment_date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">{{ $appointment->preferred_time ? substr((string) $appointment->preferred_time, 0, 5) : 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-col items-start gap-1">
                                            @if($appointment->is_emergency)
                                                <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold uppercase text-rose-700">
                                                    Emergency
                                                </span>
                                            @endif
                                            <x-ui.appointment-status-badge :appointment="$appointment" />
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            <x-ui.appointment-status-menu :appointment="$appointment" />
                                            <x-ui.button :href="route('appointments.edit', $appointment)" variant="secondary">Reschedule</x-ui.button>
                                            @if(in_array($appointment->status, ['pending', 'approved'], true))
                                                <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" x-on:submit.prevent="$dispatch('confirm-form', { form: $el, message: 'Cancel this appointment?' })">
                                                    @csrf
                                                    <x-ui.button variant="danger">Cancel</x-ui.button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $groomingAppointments->links() }}</div>
            @endif
        </x-ui.card>
    </section>

    @include('reports._print_modal', ['modalName' => 'print-report-modal'])

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <script>
        const superadminVaccinationChart = document.getElementById('superadminVaccinationChart');
        if (superadminVaccinationChart) {
            new Chart(superadminVaccinationChart, {
                type: 'line',
                data: {
                    labels: @json($vaccinationChart['labels']),
                    datasets: [
                        {
                            label: 'Vaccination Appointments',
                            data: @json($vaccinationChart['totals']),
                            borderColor: '#3B3281',
                            backgroundColor: 'rgba(59, 50, 129, 0.14)',
                            tension: 0.3,
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        const superadminCheckupChart = document.getElementById('superadminCheckupChart');
        if (superadminCheckupChart) {
            new Chart(superadminCheckupChart, {
                type: 'line',
                data: {
                    labels: @json($checkupChart['labels']),
                    datasets: [
                        {
                            label: 'Checkup Appointments',
                            data: @json($checkupChart['totals']),
                            borderColor: '#02BF95',
                            backgroundColor: 'rgba(2, 191, 149, 0.12)',
                            tension: 0.3,
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    </script>
</x-app-layout>
