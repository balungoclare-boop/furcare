<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Hospitalization;
use App\Models\Medicine;
use App\Models\Pet;
use App\Models\User;
use App\Models\Vaccination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class DashboardService
{
    public function adminOverview(): array
    {
        $windowStart = now()->subDays(6)->startOfDay();
        $windowEnd = now()->endOfDay();
        $today = now()->toDateString();
        $dueSoonEnd = now()->addDays(7)->toDateString();

        $vaccinationAppointmentQuery = Appointment::query()
            ->where('type', Appointment::TYPE_VACCINATION);
        $checkupAppointmentQuery = Appointment::query()
            ->where('type', Appointment::TYPE_CHECKUP);

        $vaccinationAppointmentSeries = (clone $vaccinationAppointmentQuery)
            ->selectRaw('DATE(appointment_date) as day, COUNT(*) as aggregate')
            ->whereBetween('appointment_date', [$windowStart->toDateString(), $windowEnd->toDateString()])
            ->groupBy('day')
            ->pluck('aggregate', 'day');
        $checkupAppointmentSeries = (clone $checkupAppointmentQuery)
            ->selectRaw('DATE(appointment_date) as day, COUNT(*) as aggregate')
            ->whereBetween('appointment_date', [$windowStart->toDateString(), $windowEnd->toDateString()])
            ->groupBy('day')
            ->pluck('aggregate', 'day');

        $appointmentsForTables = Appointment::query()
            ->with('pet:id,pet_name,owner_name,user_id');

        $appointmentsForTables
            ->latest('created_at')
            ->latest('id');

        $thisMonthStart = now()->startOfMonth()->toDateString();
        $thisMonthEnd = now()->endOfMonth()->toDateString();
        $hasHospitalizations = Schema::hasTable('hospitalizations');
        $hasMedicines = Schema::hasTable('medicines');
        $excludedLegacySystemNames = ['Clinic Admin', 'Master Admin'];
        $operationalStaff = User::query()
            ->whereIn('role', [User::ROLE_ADMIN, User::ROLE_VETERINARY_STAFF])
            ->where('is_active', true)
            ->whereNotIn('name', $excludedLegacySystemNames)
            ->orderBy('id')
            ->get();

        $staffPerformance = $operationalStaff
            ->map(fn (User $staff) => $this->buildStaffPerformanceForUser($staff));

        return [
            'metrics' => [
                'totalUsers' => User::count(),
                'totalClients' => User::where('role', User::ROLE_CLIENT)->count(),
                'totalStaff' => User::where('role', User::ROLE_VETERINARY_STAFF)->count(),
                'totalPets' => Pet::count(),
                'totalAppointments' => Appointment::count(),
                'totalVaccinations' => Vaccination::count(),
                'overdueVaccinations' => Vaccination::whereDate('next_due_date', '<', $today)->count(),
                'dueSoonVaccinations' => Vaccination::whereBetween('next_due_date', [$today, $dueSoonEnd])->count(),
                'activeHospitalizations' => $hasHospitalizations
                    ? Hospitalization::query()->where('status', Hospitalization::STATUS_ACTIVE)->count()
                    : 0,
                'lowStockMedicines' => $hasMedicines
                    ? Medicine::query()->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count()
                    : 0,
                'expiredMedicines' => $hasMedicines
                    ? Medicine::query()->whereDate('expiration_date', '<', $today)->count()
                    : 0,
            ],
            'vaccinationAnalytics' => [
                'totalAppointments' => (clone $vaccinationAppointmentQuery)->count(),
                'mostCommonVaccine' => $this->mostCommonPurpose($vaccinationAppointmentQuery),
                'thisMonthCount' => (clone $vaccinationAppointmentQuery)
                    ->whereBetween('appointment_date', [$thisMonthStart, $thisMonthEnd])
                    ->count(),
            ],
            'checkupAnalytics' => [
                'totalAppointments' => (clone $checkupAppointmentQuery)->count(),
                'mostCommonReason' => $this->mostCommonPurpose($checkupAppointmentQuery),
                'thisMonthCount' => (clone $checkupAppointmentQuery)
                    ->whereBetween('appointment_date', [$thisMonthStart, $thisMonthEnd])
                    ->count(),
            ],
            'vaccinationAppointments' => (clone $appointmentsForTables)
                ->where('type', Appointment::TYPE_VACCINATION)
                ->where('status', Appointment::STATUS_PENDING)
                ->whereNull('accepted_at')
                ->paginate(8, ['*'], 'vaccination_page')
                ->withQueryString(),
            'groomingAppointments' => (clone $appointmentsForTables)
                ->where('type', Appointment::TYPE_GROOMING)
                ->where('status', Appointment::STATUS_PENDING)
                ->whereNull('accepted_at')
                ->paginate(8, ['*'], 'grooming_page')
                ->withQueryString(),
            'vaccinationChart' => $this->buildTypeChartData($vaccinationAppointmentSeries->all()),
            'checkupChart' => $this->buildTypeChartData($checkupAppointmentSeries->all()),
            'recentActivities' => Schema::hasTable('activity_logs')
                ? ActivityLog::query()
                    ->with('user:id,name')
                    ->latest('created_at')
                    ->limit(10)
                    ->get()
                : collect(),
            'staffPerformance' => $staffPerformance,
            'activeHospitalizationsList' => $hasHospitalizations
                ? Hospitalization::query()
                    ->with('pet:id,pet_name,owner_name')
                    ->where('status', Hospitalization::STATUS_ACTIVE)
                    ->latest('admitted_date')
                    ->limit(6)
                    ->get()
                : collect(),
            'lowStockMedicinesList' => $hasMedicines
                ? Medicine::query()
                    ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                    ->orderBy('stock_quantity')
                    ->limit(6)
                    ->get()
                : collect(),
        ];
    }

    public function staffOverview(User $user): array
    {
        $windowStart = now()->subDays(6)->startOfDay();
        $windowEnd = now()->endOfDay();
        $today = now()->toDateString();
        $dueSoonEnd = now()->addDays(7)->toDateString();

        $appointments = Appointment::query()->ownedBy($user);
        $vaccinations = Vaccination::query()->ownedBy($user);

        $vaccinationAppointmentQuery = (clone $appointments)
            ->where('type', Appointment::TYPE_VACCINATION);
        $checkupAppointmentQuery = (clone $appointments)
            ->where('type', Appointment::TYPE_CHECKUP);

        $vaccinationAppointmentSeries = (clone $vaccinationAppointmentQuery)
            ->selectRaw('DATE(appointment_date) as day, COUNT(*) as aggregate')
            ->whereBetween('appointment_date', [$windowStart->toDateString(), $windowEnd->toDateString()])
            ->groupBy('day')
            ->pluck('aggregate', 'day');
        $checkupAppointmentSeries = (clone $checkupAppointmentQuery)
            ->selectRaw('DATE(appointment_date) as day, COUNT(*) as aggregate')
            ->whereBetween('appointment_date', [$windowStart->toDateString(), $windowEnd->toDateString()])
            ->groupBy('day')
            ->pluck('aggregate', 'day');

        $appointmentsForTables = Appointment::query()
            ->ownedBy($user)
            ->with('pet:id,pet_name,owner_name,user_id');

        $appointmentsForTables
            ->latest('created_at')
            ->latest('id');

        $thisMonthStart = now()->startOfMonth()->toDateString();
        $thisMonthEnd = now()->endOfMonth()->toDateString();
        $hasHospitalizations = Schema::hasTable('hospitalizations');
        $hasMedicines = Schema::hasTable('medicines');

        return [
            'metrics' => [
                'totalPets' => Pet::query()->ownedBy($user)->count(),
                'totalAppointments' => (clone $appointments)->count(),
                'totalVaccinations' => (clone $vaccinations)->count(),
                'overdueVaccinations' => (clone $vaccinations)
                    ->whereDate('next_due_date', '<', $today)
                    ->count(),
                'dueSoonVaccinations' => (clone $vaccinations)
                    ->whereBetween('next_due_date', [$today, $dueSoonEnd])
                    ->count(),
                'activeHospitalizations' => $hasHospitalizations
                    ? Hospitalization::query()
                        ->ownedBy($user)
                        ->where('status', Hospitalization::STATUS_ACTIVE)
                        ->count()
                    : 0,
                'lowStockMedicines' => $hasMedicines
                    ? Medicine::query()->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count()
                    : 0,
                'expiredMedicines' => $hasMedicines
                    ? Medicine::query()->whereDate('expiration_date', '<', $today)->count()
                    : 0,
            ],
            'vaccinationAnalytics' => [
                'totalAppointments' => (clone $vaccinationAppointmentQuery)->count(),
                'mostCommonVaccine' => $this->mostCommonPurpose($vaccinationAppointmentQuery),
                'thisMonthCount' => (clone $vaccinationAppointmentQuery)
                    ->whereBetween('appointment_date', [$thisMonthStart, $thisMonthEnd])
                    ->count(),
            ],
            'checkupAnalytics' => [
                'totalAppointments' => (clone $checkupAppointmentQuery)->count(),
                'mostCommonReason' => $this->mostCommonPurpose($checkupAppointmentQuery),
                'thisMonthCount' => (clone $checkupAppointmentQuery)
                    ->whereBetween('appointment_date', [$thisMonthStart, $thisMonthEnd])
                    ->count(),
            ],
            'vaccinationAppointments' => (clone $appointmentsForTables)
                ->where('type', Appointment::TYPE_VACCINATION)
                ->where('status', Appointment::STATUS_PENDING)
                ->whereNull('accepted_at')
                ->paginate(8, ['*'], 'vaccination_page')
                ->withQueryString(),
            'groomingAppointments' => (clone $appointmentsForTables)
                ->where('type', Appointment::TYPE_GROOMING)
                ->where('status', Appointment::STATUS_PENDING)
                ->whereNull('accepted_at')
                ->paginate(8, ['*'], 'grooming_page')
                ->withQueryString(),
            'vaccinationChart' => $this->buildTypeChartData($vaccinationAppointmentSeries->all()),
            'checkupChart' => $this->buildTypeChartData($checkupAppointmentSeries->all()),
            'staffPerformance' => $this->buildStaffPerformanceForUser($user),
            'activeHospitalizationsList' => $hasHospitalizations
                ? Hospitalization::query()
                    ->ownedBy($user)
                    ->with('pet:id,pet_name,owner_name')
                    ->where('status', Hospitalization::STATUS_ACTIVE)
                    ->latest('admitted_date')
                    ->limit(6)
                    ->get()
                : collect(),
            'lowStockMedicinesList' => $hasMedicines
                ? Medicine::query()
                    ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                    ->orderBy('stock_quantity')
                    ->limit(6)
                    ->get()
                : collect(),
        ];
    }

    public function clientOverview(User $user): array
    {
        $appointments = Appointment::query()->ownedBy($user);
        $vaccinations = Vaccination::query()->ownedBy($user);

        return [
            'metrics' => [
                'myPets' => Pet::query()->ownedBy($user)->count(),
                'myAppointments' => (clone $appointments)->count(),
                'pendingAppointments' => (clone $appointments)
                    ->where('status', Appointment::STATUS_PENDING)
                    ->whereNull('accepted_at')
                    ->count(),
                'upcomingVaccinations' => (clone $vaccinations)
                    ->whereDate('next_due_date', '>=', now()->toDateString())
                    ->count(),
                'overdueVaccinations' => (clone $vaccinations)
                    ->whereDate('next_due_date', '<', now()->toDateString())
                    ->count(),
            ],
            'upcomingAppointments' => (clone $appointments)
                ->with('pet:id,pet_name,user_id')
                ->whereDate('appointment_date', '>=', now()->toDateString())
                ->whereNotIn('status', [
                    Appointment::STATUS_COMPLETED,
                    Appointment::STATUS_CANCELLED,
                    Appointment::STATUS_REJECTED,
                ])
                ->orderBy('appointment_date')
                ->limit(8)
                ->get(),
        ];
    }

    private function mostCommonPurpose(Builder $query): string
    {
        $purpose = (clone $query)
            ->whereNotNull('purpose')
            ->where('purpose', '!=', '')
            ->selectRaw('purpose, COUNT(*) as aggregate')
            ->groupBy('purpose')
            ->orderByDesc('aggregate')
            ->value('purpose');

        return $purpose ?: 'N/A';
    }

    private function buildTypeChartData(array $series): array
    {
        $labels = [];
        $totals = [];

        foreach (range(0, 6) as $dayOffset) {
            $day = Carbon::now()->subDays($dayOffset)->toDateString();
            $labels[] = Carbon::parse($day)->format('M d');
            $totals[] = (int) ($series[$day] ?? 0);
        }

        return [
            'labels' => $labels,
            'totals' => $totals,
        ];
    }

    private function buildStaffPerformanceForUser(User $staff): array
    {
        if (! Schema::hasColumn('appointments', 'staff_id')) {
            return [
                'staff_id' => $staff->id,
                'staff_name' => $staff->name,
                'total_consultations' => 0,
                'vaccinations_administered' => 0,
                'grooming_handled' => 0,
                'completed_appointments' => 0,
            ];
        }

        $staffAppointments = Appointment::query()->where('staff_id', $staff->id);

        return [
            'staff_id' => $staff->id,
            'staff_name' => $staff->name,
            'total_consultations' => (clone $staffAppointments)
                ->where('type', Appointment::TYPE_CHECKUP)
                ->count(),
            'vaccinations_administered' => (clone $staffAppointments)
                ->where('type', Appointment::TYPE_VACCINATION)
                ->count(),
            'grooming_handled' => (clone $staffAppointments)
                ->where('type', Appointment::TYPE_GROOMING)
                ->count(),
            'completed_appointments' => (clone $staffAppointments)
                ->where('status', Appointment::STATUS_COMPLETED)
                ->count(),
        ];
    }
}
