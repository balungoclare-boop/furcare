<?php

use App\Mail\PasswordResetCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('code verification and resend forms are not nested', function () {
    $response = $this->get(route('password.verify.form', ['email' => 'client@example.test']));

    $response->assertOk()->assertSee('form="verify-reset-code-form"', false);

    $html = $response->getContent();
    $firstForm = strpos($html, '<form');
    $secondForm = strpos($html, '<form', $firstForm + 1);
    $firstFormClose = strpos($html, '</form>', $firstForm);

    expect($firstFormClose)->toBeLessThan($secondForm);
});

test('password reset code can be requested by email', function () {
    Mail::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email])
        ->assertRedirect(route('password.verify.form', ['email' => $user->email]))
        ->assertSessionHas('status', 'If an account exists for that email, we sent a 6-digit verification code.');

    Mail::assertSent(PasswordResetCodeMail::class, fn (PasswordResetCodeMail $mail): bool => $mail->hasTo($user->email));
});

test('a valid code can be verified and the reset form can be rendered', function () {
    Mail::fake();
    $user = User::factory()->create();
    $this->post('/forgot-password', ['email' => $user->email]);

    $code = null;
    Mail::assertSent(PasswordResetCodeMail::class, function (PasswordResetCodeMail $mail) use (&$code): bool {
        $code = $mail->code;

        return true;
    });

    $this->post(route('password.verify'), ['email' => $user->email, 'code' => $code])
        ->assertRedirect(route('password.reset'));

    $this->get(route('password.reset'))->assertOk();
});

test('password can be reset with a verified code', function () {
    Mail::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    $code = null;
    Mail::assertSent(PasswordResetCodeMail::class, function (PasswordResetCodeMail $mail) use (&$code): bool {
        $code = $mail->code;

        return true;
    });

    $this->post(route('password.verify'), ['email' => $user->email, 'code' => $code])
        ->assertRedirect(route('password.reset'));

    $newPassword = 'NewSecurePassword@123';
    $this->post(route('password.store'), [
        'email' => $user->email,
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('login'));

    expect(Hash::check($newPassword, $user->fresh()->password))->toBeTrue();
});

test('password reset responses do not reveal whether an email is registered', function () {
    Mail::fake();
    $registeredEmail = User::factory()->create()->email;
    $unknownEmail = 'unknown@example.test';

    $registeredResponse = $this->post('/forgot-password', ['email' => $registeredEmail]);
    $unknownResponse = $this->post('/forgot-password', ['email' => $unknownEmail]);

    $registeredResponse
        ->assertRedirect(route('password.verify.form', ['email' => $registeredEmail]))
        ->assertSessionHas('status', 'If an account exists for that email, we sent a 6-digit verification code.');
    $unknownResponse
        ->assertRedirect(route('password.verify.form', ['email' => $unknownEmail]))
        ->assertSessionHas('status', 'If an account exists for that email, we sent a 6-digit verification code.');

    Mail::assertSent(PasswordResetCodeMail::class, 1);
});

test('verification does not reveal whether an email is registered', function () {
    Mail::fake();
    $registeredEmail = User::factory()->create()->email;
    $this->post('/forgot-password', ['email' => $registeredEmail]);

    $registeredResponse = $this->post(route('password.verify'), [
        'email' => $registeredEmail,
        'code' => '123456',
    ]);
    $unknownResponse = $this->post(route('password.verify'), [
        'email' => 'unknown@example.test',
        'code' => '123456',
    ]);

    $genericError = [
        'code' => 'The code is invalid, expired, or unavailable. Please request a new one.',
    ];

    $registeredResponse->assertSessionHasErrors($genericError);
    $unknownResponse->assertSessionHasErrors($genericError);
});
