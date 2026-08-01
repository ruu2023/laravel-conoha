<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Shared Google login flow for every app listed in config('restricted_apps.apps')
 * (currently techpulse and zundamon). Registered per-app in each
 * routes/apps/{app}.php with route names "{app}.auth.google.redirect" /
 * "{app}.auth.google.callback" — Google's authorized-redirect-URI setting
 * needs an exact per-subdomain URL, so each app gets its own callback URL
 * even though they share one User/session afterward.
 */
class GoogleAuthController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        return Socialite::driver('google')
            ->redirectUrl($this->callbackUrl($request))
            ->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        $googleUser = Socialite::driver('google')
            ->redirectUrl($this->callbackUrl($request))
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

        return redirect()->route($request->appSubdomain().'.home');
    }

    private function callbackUrl(Request $request): string
    {
        return route($request->appSubdomain().'.auth.google.callback');
    }
}
