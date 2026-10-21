<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->withCount('pets')
            ->latest()
            ->paginate(12);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create');
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'role' => $request->validated('role'),
            'is_active' => (bool) $request->boolean('is_active', true),
            'password' => Hash::make($request->validated('password')),
            'must_change_password' => (bool) $request->boolean('must_change_password', true),
        ]);

        if ($user->role === User::ROLE_CLIENT) {
            $this->activityLogService->log(
                $request->user(),
                'New Client Registered',
                sprintf('Created client account for %s.', $user->name)
            );
        }

        $this->auditLogService->log(
            $request->user(),
            'created',
            'user',
            $user->id,
            sprintf('Created user account for %s.', $user->name),
            ['role' => $user->role]
        );

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);
        $originalRole = $user->role;

        if (auth()->id() === $user->id && ! $request->boolean('is_active', true)) {
            return redirect()
                ->route('admin.users.edit', $user)
                ->with('error', 'You cannot deactivate your own account.');
        }

        if (auth()->id() === $user->id && $request->validated('role') !== User::ROLE_ADMIN) {
            return redirect()
                ->route('admin.users.edit', $user)
                ->with('error', 'You cannot remove your own admin role.');
        }

        $payload = [
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'role' => $request->validated('role'),
            'is_active' => (bool) $request->boolean('is_active', true),
            'must_change_password' => (bool) $request->boolean('must_change_password'),
        ];

        if ($request->filled('password')) {
            $payload['password'] = Hash::make($request->validated('password'));
            $payload['must_change_password'] = true;
        }

        $user->update($payload);

        $this->auditLogService->log(
            $request->user(),
            'updated',
            'user',
            $user->id,
            sprintf('Updated user account for %s.', $user->name)
        );

        if ($originalRole !== $user->role) {
            $this->auditLogService->log(
                $request->user(),
                'role_changed',
                'user',
                $user->id,
                sprintf('Changed role for %s from %s to %s.', $user->name, $originalRole, $user->role),
                ['from' => $originalRole, 'to' => $user->role]
            );
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

}
