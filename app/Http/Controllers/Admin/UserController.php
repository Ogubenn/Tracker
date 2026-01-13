<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateUser($request);
        $validated['password'] = Hash::make($validated['password']);
        $validated['aktif_mi'] = $request->has('aktif_mi');

        User::create($validated);

        return $this->redirectWithSuccess('Kullanıcı başarıyla oluşturuldu.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $this->validateUser($request, $user);
        
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['aktif_mi'] = $request->has('aktif_mi');
        $user->update($validated);

        return $this->redirectWithSuccess('Kullanıcı başarıyla güncellendi.');
    }

    public function destroy(User $user)
    {
        if ($this->isCurrentUser($user)) {
            return $this->redirectWithError('Kendi hesabınızı silemezsiniz.');
        }

        $user->delete();

        return $this->redirectWithSuccess('Kullanıcı başarıyla silindi.');
    }
    
    public function toggleQrGorunur(User $user)
    {
        $user->update(['qr_gorunur' => !$user->qr_gorunur]);
        
        return redirect()->back()->with('success', 'QR görünürlüğü güncellendi.');
    }

    public function toggleMailAlsin(User $user)
    {
        $user->update(['mail_alsin' => !$user->mail_alsin]);
        
        $durum = $user->mail_alsin ? 'açıldı' : 'kapatıldı';
        return redirect()->back()->with('success', "Mail bildirimleri {$durum}.");
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'ad' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                $user ? Rule::unique('users')->ignore($user->id) : 'unique:users,email'
            ],
            'password' => $user ? 'nullable|string|min:6|confirmed' : 'required|string|min:6|confirmed',
            'rol' => 'required|in:admin,personel',
        ]);
    }

    private function isCurrentUser(User $user): bool
    {
        return $user->id === auth()->id();
    }

    private function redirectWithSuccess(string $message)
    {
        return redirect()->route('admin.users.index')->with('success', $message);
    }

    private function redirectWithError(string $message)
    {
        return redirect()->route('admin.users.index')->with('error', $message);
    }
}
