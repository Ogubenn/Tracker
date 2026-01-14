<?php

namespace App\Http\Controllers\Personel;

use App\Http\Controllers\Controller;
use App\Models\Bina;
use App\Models\KontrolKaydi;
use App\Models\KontrolMaddesi;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $binalar = Bina::aktif()
            ->with([
                'aktifKontrolMaddeleri' => fn($query) => $query->where('aktif_mi', true)->orderBy('sira')
            ])
            ->get();

        $bugunYapilacakKontroller = $this->groupPendingKontrollerByBina($binalar);

        return view('personel.dashboard', compact('bugunYapilacakKontroller'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateKontrolData($request);

        if ($this->hasExistingRecord($validated['kontrol_maddesi_id'])) {
            return back()->with('error', 'Bu kontrol bugün zaten yapılmış!');
        }

        try {
            DB::beginTransaction();
            $this->saveKontrolKaydi($validated);
            DB::commit();

            return back()->with('success', 'Kontrol başarıyla kaydedildi!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bir hata oluştu: ' . $e->getMessage());
        }
    }

    private function groupPendingKontrollerByBina($binalar): array
    {
        $bugunYapilacakKontroller = [];

        foreach ($binalar as $bina) {
            foreach ($bina->aktifKontrolMaddeleri as $kontrolMaddesi) {
                if ($this->isKontrolPendingToday($kontrolMaddesi)) {
                    $this->addKontrolToGroup($bugunYapilacakKontroller, $bina, $kontrolMaddesi);
                }
            }
        }

        return $bugunYapilacakKontroller;
    }

    private function isKontrolPendingToday(KontrolMaddesi $kontrolMaddesi): bool
    {
        return $kontrolMaddesi->bugunYapilmaliMi() && !$kontrolMaddesi->bugunKaydiVarMi();
    }

    private function addKontrolToGroup(array &$kontroller, Bina $bina, KontrolMaddesi $kontrolMaddesi): void
    {
        if (!isset($kontroller[$bina->id])) {
            $kontroller[$bina->id] = [
                'bina' => $bina,
                'kontroller' => []
            ];
        }

        $kontroller[$bina->id]['kontroller'][] = $kontrolMaddesi;
    }

    private function validateKontrolData(Request $request): array
    {
        return $request->validate([
            'kontrol_maddesi_id' => 'required|exists:kontrol_maddeleri,id',
            'girilen_deger' => 'required',
        ]);
    }

    private function hasExistingRecord(int $kontrolMaddesiId): bool
    {
        return KontrolKaydi::where('kontrol_maddesi_id', $kontrolMaddesiId)
            ->whereDate('tarih', Carbon::today())
            ->exists();
    }

    private function saveKontrolKaydi(array $validated): void
    {
        KontrolKaydi::create([
            'kontrol_maddesi_id' => $validated['kontrol_maddesi_id'],
            'tarih' => Carbon::today(),
            'girilen_deger' => $validated['girilen_deger'],
            'yapan_kullanici_id' => Auth::id(),
        ]);
    }
}
