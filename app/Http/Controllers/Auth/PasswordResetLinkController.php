<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetCodeMail;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset code request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower($validated['email']);
        $userExists = User::query()->where('email', $email)->exists();

        if (! $userExists) {
            return redirect()
                ->route('password.verify.form', ['email' => $email])
                ->with('status', __('If an account exists for that email, we sent a 6-digit verification code.'));
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = Carbon::now()->addMinutes(5);

        PasswordResetCode::updateOrCreate(
            ['email' => $email],
            [
                'code_hash' => Hash::make($code),
                'expires_at' => $expiresAt,
                'attempts' => 0,
            ]
        );

        Mail::to($email)->send(
            new PasswordResetCodeMail($code, $expiresAt->diffForHumans(now(), ['parts' => 2]))
        );

        return redirect()
            ->route('password.verify.form', ['email' => $email])
            ->with('status', __('If an account exists for that email, we sent a 6-digit verification code.'));
    }
}
