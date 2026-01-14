<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_DURATION = 300;
    private const REMEMBER_DURATION_MINUTES = 525600;

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $rateLimiterKey = $this->getRateLimiterKey($request);
        
        $this->checkRateLimit($rateLimiterKey);
        
        $credentials = $this->validateCredentials($request);
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            return $this->handleSuccessfulLogin($request, $rateLimiterKey, $remember);
        }

        return $this->handleFailedLogin($rateLimiterKey);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function getRateLimiterKey(Request $request): string
    {
        return 'login.' . $request->ip();
    }

    private function checkRateLimit(string $key): void
    {
        if (RateLimiter::tooManyAttempts($key, self::MAX_LOGIN_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);
            
            throw ValidationException::withMessages([
                'email' => "Çok fazla giriş denemesi yaptınız. Lütfen {$seconds} saniye sonra tekrar deneyin.",
            ]);
        }
    }

    private function validateCredentials(Request $request): array
    {
        return $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
    }

    private function handleSuccessfulLogin(Request $request, string $rateLimiterKey, bool $remember)
    {
        RateLimiter::clear($rateLimiterKey);
        $request->session()->regenerate();

        $this->handleRememberMe($request->email, $remember);

        return $this->redirectUserBasedOnRole(Auth::user());
    }

    private function handleRememberMe(string $email, bool $remember): void
    {
        if ($remember) {
            cookie()->queue('remember_email', $email, self::REMEMBER_DURATION_MINUTES);
        } else {
            cookie()->queue(cookie()->forget('remember_email'));
        }
    }

    private function redirectUserBasedOnRole($user)
    {
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }
        
        return redirect()->intended(route('personel.dashboard'));
    }

    private function handleFailedLogin(string $rateLimiterKey)
    {
        RateLimiter::hit($rateLimiterKey, self::LOCKOUT_DURATION);

        return back()
            ->withErrors(['email' => 'Giriş bilgileri hatalı.'])
            ->onlyInput('email');
    }
}
