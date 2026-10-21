<?php

namespace App\Http\Controllers;

use App\Models\Hospitalization;
use App\Models\HospitalizationProgressNote;
use App\Models\Pet;
use App\Services\AuditLogService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HospitalizationController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request): View
    {
        if (! $this->tableExists()) {
            $activeHospitalizations = new LengthAwarePaginator(
                [],
                0,
                8,
                $request->integer('active_page', 1),
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                    'pageName' => 'active_page',
                ]
            );

            $dischargedHospitalizations = new LengthAwarePaginator(
                [],
                0,
                8,
                $request->integer('discharged_page', 1),
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                    'pageName' => 'discharged_page',
                ]
            );

            return view('hospitalizations.index', [
                'activeHospitalizations' => $activeHospitalizations,
                'dischargedHospitalizations' => $dischargedHospitalizations,
                'schemaReady' => false,
            ]);
        }

        $query = Hospitalization::query()
            ->with('pet:id,pet_name,owner_name')
            ->ownedBy($request->user())
            ->latest('admitted_date')
            ->latest('id');

        $activeHospitalizations = (clone $query)
            ->where('status', Hospitalization::STATUS_ACTIVE)
            ->paginate(8, ['*'], 'active_page')
            ->withQueryString();

        $dischargedHospitalizations = (clone $query)
            ->where('status', Hospitalization::STATUS_DISCHARGED)
            ->paginate(8, ['*'], 'discharged_page')
            ->withQueryString();

        return view('hospitalizations.index', [
            'activeHospitalizations' => $activeHospitalizations,
            'dischargedHospitalizations' => $dischargedHospitalizations,
            'schemaReady' => true,
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        if (! $this->tableExists()) {
            return redirect()
                ->route('hospitalizations.index')
                ->with('error', 'Hospitalization module is not ready yet. Run `php artisan migrate` first.');
        }

        return view('hospitalizations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $this->tableExists()) {
            return redirect()
                ->route('hospitalizations.index')
                ->with('error', 'Hospitalization module is not ready yet. Run `php artisan migrate` first.');
        }

        $payload = $this->validatedPayload($request);
        $pet = $this->resolvePetFromPayload($payload);
        $payload['pet_id'] = $pet->id;
        unset($payload['pet_name'], $payload['owner_name']);
        $payload['user_id'] = $request->user()->id;

        if ($payload['status'] === Hospitalization::STATUS_ACTIVE) {
            $payload['discharge_date'] = null;
            $payload['discharge_summary'] = null;
        }

        $hospitalization = Hospitalization::query()->create($payload);
        $this->auditLogService->log(
            $request->user(),
            'created',
            'hospitalization',
            $hospitalization->id,
            sprintf('Created hospitalization record for %s.', $hospitalization->pet?->pet_name ?? 'Unknown pet')
        );

        return redirect()
            ->route('hospitalizations.show', $hospitalization)
            ->with('success', 'Hospitalization record created successfully.');
    }

    public function show(Request $request, Hospitalization $hospitalization): View
    {
        $this->ensureAccessible($request, $hospitalization);
        $hospitalization->load([
            'pet:id,pet_name,owner_name',
            'progressNotes' => fn ($query) => $query->with('user:id,name')->latest('note_date')->latest('id'),
        ]);

        return view('hospitalizations.show', compact('hospitalization'));
    }

    public function edit(Request $request, Hospitalization $hospitalization): View
    {
        $this->ensureAccessible($request, $hospitalization);

        return view('hospitalizations.edit', compact('hospitalization'));
    }

    public function update(Request $request, Hospitalization $hospitalization): RedirectResponse
    {
        $this->ensureAccessible($request, $hospitalization);

        $payload = $this->validatedPayload($request);
        $pet = $this->resolvePetFromPayload($payload);
        $payload['pet_id'] = $pet->id;
        unset($payload['pet_name'], $payload['owner_name']);

        if ($payload['status'] === Hospitalization::STATUS_ACTIVE) {
            $payload['discharge_date'] = null;
            $payload['discharge_summary'] = null;
        }

        $hospitalization->update($payload);
        $this->auditLogService->log(
            $request->user(),
            'updated',
            'hospitalization',
            $hospitalization->id,
            sprintf('Updated hospitalization record for %s.', $hospitalization->pet?->pet_name ?? 'Unknown pet')
        );

        return redirect()
            ->route('hospitalizations.index')
            ->with('success', 'Hospitalization record updated successfully.');
    }

    public function storeProgressNote(Request $request, Hospitalization $hospitalization): RedirectResponse
    {
        $this->ensureAccessible($request, $hospitalization);

        $payload = $request->validate([
            'note_date' => ['required', 'date'],
            'notes' => ['required', 'string', 'max:2000'],
        ]);

        HospitalizationProgressNote::query()->create([
            'hospitalization_id' => $hospitalization->id,
            'user_id' => $request->user()->id,
            'note_date' => $payload['note_date'],
            'notes' => $payload['notes'],
        ]);

        $this->auditLogService->log(
            $request->user(),
            'updated',
            'hospitalization',
            $hospitalization->id,
            sprintf('Added daily progress note for %s.', $hospitalization->pet?->pet_name ?? 'Unknown pet')
        );

        return back()->with('success', 'Progress note added successfully.');
    }

    private function validatedPayload(Request $request): array
    {
        $payload = $request->validate([
            'pet_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'admitted_date' => ['required', 'date'],
            'discharge_date' => ['nullable', 'date', 'after_or_equal:admitted_date'],
            'status' => ['required', Rule::in([Hospitalization::STATUS_ACTIVE, Hospitalization::STATUS_DISCHARGED])],
            'notes' => ['nullable', 'string', 'max:2000'],
            'medication_schedule' => ['nullable', 'string', 'max:2000'],
            'discharge_summary' => ['nullable', 'string', 'max:2000'],
        ]);

        $payload['pet_name'] = trim((string) $payload['pet_name']);
        $payload['owner_name'] = trim((string) $payload['owner_name']);

        return $payload;
    }

    private function resolvePetFromPayload(array $payload): Pet
    {
        $petName = (string) $payload['pet_name'];
        $ownerName = (string) $payload['owner_name'];

        $existingPet = Pet::query()
            ->where('pet_name', $petName)
            ->where('owner_name', $ownerName)
            ->first();

        if ($existingPet) {
            return $existingPet;
        }

        return Pet::query()->create([
            'user_id' => null,
            'owner_name' => $ownerName,
            'contact_number' => 'N/A',
            'pet_name' => $petName,
            'species' => 'Unknown',
            'breed' => null,
            'sex' => null,
            'age' => null,
            'age_value' => null,
            'age_type' => null,
        ]);
    }

    private function ensureAccessible(Request $request, Hospitalization $hospitalization): void
    {
        if ($request->user()->isStaffOrAdmin()) {
            return;
        }

        if ((int) $hospitalization->user_id !== (int) $request->user()->id) {
            abort(403);
        }
    }

    private function tableExists(): bool
    {
        return Schema::hasTable('hospitalizations');
    }
}
