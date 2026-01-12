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
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'rol' => 'required|in:admin,personel',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['aktif_mi'] = $request->has('aktif_mi');

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'rol' => 'required|in:admin,personel',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['aktif_mi'] = $request->has('aktif_mi');

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    public function destroy(User $user)
    {
        // Kendi hesabını silmeyi engelle
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Kendi hesabınızı silemezsiniz.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');
    }
    
    /**
     * QR görünürlüğünü değiştir
     */
    public function toggleQrGorunur(User $user)
    {
        $user->qr_gorunur = !$user->qr_gorunur;
        $user->save();
        
        return redirect()->back()->with('success', 'QR görünürlüğü güncellendi.');
    }
}
