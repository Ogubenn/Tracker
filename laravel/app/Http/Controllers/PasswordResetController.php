<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    private const TOKEN_EXPIRY_MINUTES = 60;

    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Bu e-posta adresi sistemde kayıtlı değil.');
        }

        if (!$user->isActive()) {
            return back()->with('error', 'Hesabınız aktif değil. Lütfen yöneticinizle iletişime geçin.');
        }

        $token = $this->createPasswordResetToken($user);
        $user->notify(new ResetPasswordNotification($token));

        return back()->with('success', 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.');
    }

    public function showResetForm(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $this->validateResetRequest($request);

        $resetRecord = $this->findValidResetToken($validated['token'], $validated['email']);

        if (!$resetRecord) {
            return back()->with('error', 'Geçersiz veya süresi dolmuş şifre sıfırlama bağlantısı.');
        }

        $this->updateUserPassword($validated['email'], $validated['password']);
        $this->deleteResetToken($validated['email']);

        return redirect()->route('login')->with('success', 'Şifreniz başarıyla değiştirildi. Giriş yapabilirsiniz.');
    }

    private function createPasswordResetToken(User $user): string
    {
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        return $token;
    }

    private function validateResetRequest(Request $request): array
    {
        return $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);
    }

    private function findValidResetToken(string $token, string $email): ?object
    {
        $resetRecords = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->get();

        foreach ($resetRecords as $record) {
            if ($this->isTokenValid($record, $token)) {
                return $record;
            }
        }

        return null;
    }

    private function isTokenValid(object $record, string $token): bool
    {
        if (!Hash::check($token, $record->token)) {
            return false;
        }

        $createdAt = Carbon::parse($record->created_at);
        return !$createdAt->addMinutes(self::TOKEN_EXPIRY_MINUTES)->isPast();
    }

    private function updateUserPassword(string $email, string $password): void
    {
        User::where('email', $email)->update([
            'password' => Hash::make($password),
        ]);
    }

    private function deleteResetToken(string $email): void
    {
        DB::table('password_reset_tokens')->where('email', $email)->delete();
    }
}
