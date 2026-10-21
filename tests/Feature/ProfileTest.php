<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('delete routes are unavailable and profile deletion is disabled', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile');

    $response->assertMethodNotAllowed();
    $this->assertAuthenticatedAs($user);
    $this->assertNotNull($user->fresh());

    foreach ([
        'profile.destroy',
        'admin.users.destroy',
        'pets.destroy',
        'appointments.destroy',
        'vaccinations.destroy',
        'hospitalizations.destroy',
        'medicines.destroy',
        'announcements.destroy',
        'reports.destroy',
    ] as $routeName) {
        expect(Route::has($routeName))->toBeFalse();
    }
});
