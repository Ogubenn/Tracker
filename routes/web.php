<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\BinaController;
use App\Http\Controllers\Admin\KontrolMaddesiController;
use App\Http\Controllers\Admin\KontrolKaydiController;
use App\Http\Controllers\Admin\RaporController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PublicKontrolController;
use App\Http\Controllers\Personel\DashboardController as PersonelDashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('personel.dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public QR Kod Kontrol Sistemi (Login gerektirmez)
Route::get('/kontrol/bina/{uuid}', [PublicKontrolController::class, 'index'])->name('public.kontrol.index');
Route::post('/kontrol/bina/{uuid}', [PublicKontrolController::class, 'store'])->name('public.kontrol.store');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    
    // Kullanıcılar
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle-qr', [UserController::class, 'toggleQrGorunur'])->name('users.toggle-qr');
    
    // Binalar - Bulk delete ÖNCELİKLE tanımlanmalı
    Route::delete('/binalar/bulk-delete', [BinaController::class, 'bulkDestroy'])->name('binalar.bulk-delete');
    Route::post('/binalar/{bina}/regenerate-qr', [BinaController::class, 'regenerateQr'])->name('binalar.regenerate-qr');
    Route::resource('binalar', BinaController::class)->parameters(['binalar' => 'bina']);
    
    // Kontrol Kayıtları (Onay Sistemi)
    Route::get('/kontrol-kayitlari', [KontrolKaydiController::class, 'index'])->name('kontrol-kayitlari.index');
    Route::get('/kontrol-kayitlari/{id}', [KontrolKaydiController::class, 'show'])->name('kontrol-kayitlari.show');
    Route::post('/kontrol-kayitlari/{id}/onayla', [KontrolKaydiController::class, 'onayla'])->name('kontrol-kayitlari.onayla');
    Route::post('/kontrol-kayitlari/{id}/reddet', [KontrolKaydiController::class, 'reddet'])->name('kontrol-kayitlari.reddet');
    Route::post('/kontrol-kayitlari/toplu-onayla', [KontrolKaydiController::class, 'topluOnayla'])->name('kontrol-kayitlari.toplu-onayla');
    
    // Kontrol Maddeleri - Bulk delete ÖNCELİKLE tanımlanmalı
    Route::delete('/kontrol-maddeleri/bulk-delete', [KontrolMaddesiController::class, 'bulkDestroy'])->name('kontrol-maddeleri.bulk-delete');
    Route::resource('kontrol-maddeleri', KontrolMaddesiController::class)->parameters(['kontrol-maddeleri' => 'kontrol_maddesi']);
    
    // İstatistikler
    Route::get('/istatistikler', function() {
        return view('admin.istatistikler.index');
    })->name('istatistikler.index');
    
    // Raporlar
    Route::get('/raporlar', [RaporController::class, 'index'])->name('raporlar.index');
});

// Personel Routes
Route::middleware(['auth', 'personel'])->prefix('personel')->name('personel.')->group(function () {
    Route::get('/dashboard', [PersonelDashboard::class, 'index'])->name('dashboard');
    Route::post('/kontrol-kaydet', [PersonelDashboard::class, 'store'])->name('kontrol.store');
});
