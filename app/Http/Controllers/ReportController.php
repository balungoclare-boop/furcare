<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportStoreRequest;
use App\Http\Requests\ReportUpdateRequest;
use App\Models\MedicalRecord;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Report::class);

        $reports = Report::query()
            ->with('user:id,name')
            ->ownedBy($request->user())
            ->latest()
            ->paginate(10);

        return view('reports.index', compact('reports'));
    }

    public function create(): View
    {
        $this->authorize('create', Report::class);

        return view('reports.create');
    }

    public function store(ReportStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Report::class);

        $report = Report::create(array_merge(
            $request->validated(),
            ['user_id' => $request->user()->id]
        ));

        return redirect()
            ->route('reports.show', $report)
            ->with('success', 'Report created successfully.');
    }

    public function show(Report $report): View
    {
        $this->authorize('view', $report);
        $report->load('user:id,name');

        return view('reports.show', compact('report'));
    }

    public function edit(Report $report): View
    {
        $this->authorize('update', $report);

        return view('reports.edit', compact('report'));
    }

    public function update(ReportUpdateRequest $request, Report $report): RedirectResponse
    {
        $this->authorize('update', $report);

        $report->update($request->validated());

        return redirect()
            ->route('reports.index')
            ->with('success', 'Report updated successfully.');
    }

    public function generateReport(Request $request): View
    {
        $this->authorize('viewAny', Report::class);

        $validated = $request->validate([
            'report_type' => ['required', Rule::in(['monthly', 'custom'])],
            'start_date' => ['nullable', 'required_if:report_type,custom', 'date_format:m/d/Y'],
            'end_date' => ['nullable', 'required_if:report_type,custom', 'date_format:m/d/Y', 'after_or_equal:start_date'],
        ]);

        $reportType = $validated['report_type'];

        $periodStart = $reportType === 'monthly'
            ? now()->startOfMonth()
            : Carbon::createFromFormat('m/d/Y', (string) $validated['start_date'])->startOfDay();

        $periodEnd = $reportType === 'monthly'
            ? now()->endOfMonth()
            : Carbon::createFromFormat('m/d/Y', (string) $validated['end_date'])->endOfDay();

        $user = $request->user();
        $scopedRecords = $this->scopedMedicalRecords($user)
            ->whereDate('visit_date', '>=', $periodStart->toDateString())
            ->whereDate('visit_date', '<=', $periodEnd->toDateString());

        $totalConsultations = (clone $scopedRecords)->count();
        $servicesRendered = (clone $scopedRecords)
            ->whereNotNull('treatment')
            ->where('treatment', '!=', '')
            ->count();

        $periodPetIds = (clone $scopedRecords)
            ->whereNotNull('pet_id')
            ->distinct()
            ->pluck('pet_id')
            ->filter();

        $firstVisitByPet = $this->scopedMedicalRecords($user)
            ->whereNotNull('pet_id')
            ->selectRaw('pet_id, MIN(visit_date) as first_visit_date')
            ->groupBy('pet_id')
            ->get()
            ->keyBy('pet_id');

        $newPatients = $periodPetIds
            ->filter(function ($petId) use ($firstVisitByPet, $periodStart, $periodEnd): bool {
                $firstVisitDate = $firstVisitByPet->get($petId)?->first_visit_date;

                if (! $firstVisitDate) {
                    return false;
                }

                return $firstVisitDate >= $periodStart->toDateString()
                    && $firstVisitDate <= $periodEnd->toDateString();
            })
            ->count();

        $returningPatients = max($periodPetIds->count() - $newPatients, 0);

        $commonCases = (clone $scopedRecords)
            ->selectRaw("COALESCE(NULLIF(TRIM(diagnosis), ''), NULLIF(TRIM(complaint), ''), 'Unspecified') as case_name, COUNT(*) as total")
            ->groupBy('case_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('reports.print', [
            'clinicName' => config('app.name', 'Vet Clinic System'),
            'reportTitle' => $reportType === 'monthly'
                ? 'Monthly Clinic Performance Snapshot'
                : 'Custom Clinic Performance Snapshot',
            'reportType' => $reportType,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'periodLabel' => $periodStart->format('m/d/Y').' - '.$periodEnd->format('m/d/Y'),
            'summary' => [
                'totalConsultations' => $totalConsultations,
                'newPatients' => $newPatients,
                'returningPatients' => $returningPatients,
                'servicesRendered' => $servicesRendered,
            ],
            'commonCases' => $commonCases,
            'preparedByName' => $user->name,
            'preparedByRole' => (string) Str::of((string) $user->role)->replace('_', ' ')->title(),
            'generatedAt' => now(),
        ]);
    }

    private function scopedMedicalRecords(User $user): Builder
    {
        $query = MedicalRecord::query();

        if ($user->isVeterinaryStaff()) {
            if (! Schema::hasColumn('medical_records', 'user_id')) {
                return $query->whereRaw('1 = 0');
            }

            $query->where('user_id', $user->id);
        }

        return $query;
    }
}
