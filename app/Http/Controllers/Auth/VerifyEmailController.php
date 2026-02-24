<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        Log::info('===== VERIFY EMAIL CONTROLLER =====', [
            'user_id' => $request->user()?->id,
            'user_email' => $request->user()?->email,
            'has_verified_email' => $request->user()?->hasVerifiedEmail(),
            'route_id' => $request->route('id'),
            'route_hash' => $request->route('hash'),
            'expected_hash' => sha1($request->user()?->getEmailForVerification()),
        ]);

        if ($request->user()->hasVerifiedEmail()) {
            Log::info('Email already verified, redirecting');
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            Log::info('Email marked as verified');
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
