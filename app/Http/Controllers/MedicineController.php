<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MedicineController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request): View
    {
        if (! $this->tableExists()) {
            $medicines = new LengthAwarePaginator(
                [],
                0,
                12,
                $request->integer('page', 1),
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return view('medicines.index', [
                'medicines' => $medicines,
                'lowStockCount' => 0,
                'expiredCount' => 0,
                'schemaReady' => false,
            ]);
        }

        $medicines = Medicine::query()
            ->orderBy('name')
            ->paginate(12);

        $lowStockCount = Medicine::query()
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();
        $expiredCount = Medicine::query()
            ->whereDate('expiration_date', '<', now()->toDateString())
            ->count();

        return view('medicines.index', [
            'medicines' => $medicines,
            'lowStockCount' => $lowStockCount,
            'expiredCount' => $expiredCount,
            'schemaReady' => true,
        ]);
    }

    public function create(): View|RedirectResponse
    {
        if (! $this->tableExists()) {
            return redirect()
                ->route('medicines.index')
                ->with('error', 'Medicines module is not ready yet. Run `php artisan migrate` first.');
        }

        return view('medicines.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $this->tableExists()) {
            return redirect()
                ->route('medicines.index')
                ->with('error', 'Medicines module is not ready yet. Run `php artisan migrate` first.');
        }

        $payload = $this->validatedPayload($request);
        $stockAdjustmentReason = $payload['stock_adjustment_reason'] ?? null;
        unset($payload['stock_adjustment_reason']);
        $medicine = Medicine::query()->create($payload);
        $this->auditLogService->log(
            $request->user(),
            'created',
            'medicine',
            $medicine->id,
            sprintf('Created medicine inventory record for %s.', $medicine->name),
            [
                'initial_stock_quantity' => $medicine->stock_quantity,
                'stock_adjustment_reason' => $stockAdjustmentReason,
            ]
        );

        return redirect()
            ->route('medicines.edit', $medicine)
            ->with('success', 'Medicine added successfully.');
    }

    public function edit(Medicine $medicine): View
    {
        return view('medicines.edit', compact('medicine'));
    }

    public function show(Medicine $medicine): View
    {
        return view('medicines.show', compact('medicine'));
    }

    public function update(Request $request, Medicine $medicine): RedirectResponse
    {
        $previousStockQuantity = $medicine->stock_quantity;
        $payload = $this->validatedPayload($request, $medicine);
        $stockAdjustmentReason = $payload['stock_adjustment_reason'] ?? null;
        unset($payload['stock_adjustment_reason']);
        $medicine->update($payload);
        $stockChanged = $previousStockQuantity !== $medicine->stock_quantity;

        $this->auditLogService->log(
            $request->user(),
            $stockChanged ? 'stock_adjusted' : 'updated',
            'medicine',
            $medicine->id,
            $stockChanged
                ? sprintf(
                    'Adjusted stock for %s from %d to %d.',
                    $medicine->name,
                    $previousStockQuantity,
                    $medicine->stock_quantity
                )
                : sprintf('Updated medicine inventory record for %s.', $medicine->name),
            $stockChanged ? [
                'previous_stock_quantity' => $previousStockQuantity,
                'new_stock_quantity' => $medicine->stock_quantity,
                'quantity_change' => $medicine->stock_quantity - $previousStockQuantity,
                'reason' => $stockAdjustmentReason,
            ] : []
        );

        return redirect()
            ->route('medicines.index')
            ->with('success', 'Medicine updated successfully.');
    }

    private function validatedPayload(Request $request, ?Medicine $medicine = null): array
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'expiration_date' => ['nullable', 'date'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'stock_adjustment_reason' => [
                Rule::requiredIf(fn (): bool => $medicine === null
                    ? $request->integer('stock_quantity') > 0
                    : $request->integer('stock_quantity') !== $medicine->stock_quantity),
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        return $payload;
    }

    private function tableExists(): bool
    {
        return Schema::hasTable('medicines');
    }
}
