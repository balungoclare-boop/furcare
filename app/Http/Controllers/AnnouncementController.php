<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(): View
    {
        $schemaReady = $this->schemaReady();

        $announcements = $schemaReady
            ? Announcement::query()
                ->with('creator:id,name')
                ->orderedForWelcome()
                ->paginate(10)
            : collect();

        return view('announcements.index', compact('announcements', 'schemaReady'));
    }

    public function create(): RedirectResponse|View
    {
        if (! $this->schemaReady()) {
            return redirect()
                ->route('announcements.index')
                ->with('error', 'Announcements module is not ready yet. Run `php artisan migrate` first.');
        }

        return view('announcements.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $this->schemaReady()) {
            return redirect()
                ->route('announcements.index')
                ->with('error', 'Announcements module is not ready yet. Run `php artisan migrate` first.');
        }

        $payload = $this->validatedPayload($request);
        $payload['created_by'] = $request->user()->id;
        $payload['role'] = $request->user()->role;
        $payload['publish_at'] = $payload['publish_at'] ?? now(config('app.timezone'));

        if ($request->hasFile('image')) {
            $payload['image_path'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement = Announcement::query()->create($payload);

        $this->auditLogService->log(
            $request->user(),
            'created',
            'announcement',
            $announcement->id,
            sprintf('Created announcement: %s', $announcement->title)
        );

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement posted successfully.');
    }

    public function edit(Announcement $announcement): View
    {
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $payload = $this->validatedPayload($request, true);
        $payload['publish_at'] = $payload['publish_at'] ?? now(config('app.timezone'));

        if ($request->hasFile('image')) {
            if ($announcement->image_path) {
                Storage::disk('public')->delete($announcement->image_path);
            }

            $payload['image_path'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement->update($payload);

        $this->auditLogService->log(
            $request->user(),
            'updated',
            'announcement',
            $announcement->id,
            sprintf('Updated announcement: %s', $announcement->title)
        );

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    private function validatedPayload(Request $request, bool $isUpdate = false): array
    {
        $imageRules = ['nullable'];

        if (! $isUpdate) {
            $imageRules[] = 'sometimes';
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:3000'],
            'is_pinned' => ['nullable', 'boolean'],
            'priority' => ['required', 'in:'.Announcement::PRIORITY_NORMAL.','.Announcement::PRIORITY_IMPORTANT],
            'publish_at' => ['nullable', 'date'],
            'expires_at' => [
                'nullable',
                'date',
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    $publishAt = $request->input('publish_at');
                    if (! $publishAt || ! $value) {
                        return;
                    }

                    $timezone = config('app.timezone');
                    $publishAtDateTime = Carbon::parse((string) $publishAt, $timezone);
                    $expiresAtDateTime = Carbon::parse((string) $value, $timezone);

                    if ($expiresAtDateTime->lessThanOrEqualTo($publishAtDateTime)) {
                        $fail('The expiration date must be later than the publish date.');
                    }
                },
            ],
            'image' => array_merge($imageRules, ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']),
        ]);

        unset($validated['image']);
        $validated['is_pinned'] = $request->boolean('is_pinned');

        $timezone = config('app.timezone');
        foreach (['publish_at', 'expires_at'] as $field) {
            if (! empty($validated[$field])) {
                $validated[$field] = Carbon::parse((string) $validated[$field], $timezone);
            }
        }

        return $validated;
    }

    private function schemaReady(): bool
    {
        if (! Schema::hasTable('announcements')) {
            return false;
        }

        return Schema::hasColumns('announcements', [
            'is_pinned',
            'priority',
            'publish_at',
            'expires_at',
        ]);
    }
}
