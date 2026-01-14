<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['aktif_mi'] = $request->has('aktif_mi');

        $user = User::create($validated);
        
        ActivityLog::log('created', 'App\Models\User', $user->id, null, $user->toArray(), 'Yeni kullanıcı oluşturuldu: ' . $user->ad);

        return $this->redirectWithSuccess('Kullanıcı başarıyla oluşturuldu.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $oldData = $user->toArray();
        $validated = $request->validated();
        
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['aktif_mi'] = $request->has('aktif_mi');
        $user->update($validated);
        
        ActivityLog::log('updated', 'App\Models\User', $user->id, $oldData, $user->toArray(), 'Kullanıcı güncellendi: ' . $user->ad);

        return $this->redirectWithSuccess('Kullanıcı başarıyla güncellendi.');
    }

    public function destroy(User $user)
    {
        if ($this->isCurrentUser($user)) {
            return $this->redirectWithError('Kendi hesabınızı silemezsiniz.');
        }

        $userAd = $user->ad;
        $oldData = $user->toArray();
        $user->delete();
        
        ActivityLog::log('deleted', 'App\Models\User', $user->id, $oldData, null, 'Kullanıcı silindi: ' . $userAd);

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
