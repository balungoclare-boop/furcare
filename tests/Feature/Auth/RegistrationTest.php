<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200)
        ->assertSee('Use at least 8 characters.')
        ->assertSee('registration-password-guidance', false);
});

test('clients can self register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('login', absolute: false));
    $response->assertSessionHas('status');
    expect(User::query()->where('email', 'test@example.com')->value('role'))->toBe(User::ROLE_CLIENT);
});
