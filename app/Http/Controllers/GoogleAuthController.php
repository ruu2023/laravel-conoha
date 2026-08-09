<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Shared Google login flow for the gated apps (currently techpulse and
 * zundamon). Login only ever happens on the "login" hub
 * (routes/apps/login.php), so the redirect/callback URLs are always
 * login.* — Google's authorized-redirect-URI setting needs one exact URL,
 * and routing everyone through this single hub means only one needs
 * registering instead of one per gated app.
 */
class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->redirectUrl($this->callbackUrl())
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')
            ->redirectUrl($this->callbackUrl())
            ->user();

        $email = strtolower($googleUser->getEmail());

        abort_unless(
            in_array($email, config('restricted_apps.allowed_emails', []), true),
            403,
            'このアプリの利用権限がありません。',
        );

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $googleUser->getName() ?: $email, 'password' => Hash::make(Str::random(40))],
        );

        Auth::login($user, remember: true);

        return redirect()->route('login.home');
    }

    private function callbackUrl(): string
    {
        return route('login.auth.google.callback');
    }
}
