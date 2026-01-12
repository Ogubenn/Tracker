<?php

namespace App\Http\Controllers\Personel;

use App\Http\Controllers\Controller;
use App\Models\Bina;
use App\Models\KontrolKaydi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Bugün yapılması gereken kontrolleri getir
        $binalar = Bina::aktif()
            ->with([
                'aktifKontrolMaddeleri' => function ($query) {
                    $query->where('aktif_mi', true)
                        ->orderBy('sira');
                }
            ])
            ->get();

        // Her kontrol maddesi için bugün yapılmalı mı kontrolü yap
        $bugunYapilacakKontroller = [];
        
        foreach ($binalar as $bina) {
            foreach ($bina->aktifKontrolMaddeleri as $kontrolMaddesi) {
                if ($kontrolMaddesi->bugunYapilmaliMi() && !$kontrolMaddesi->bugunKaydiVarMi()) {
                    if (!isset($bugunYapilacakKontroller[$bina->id])) {
                        $bugunYapilacakKontroller[$bina->id] = [
                            'bina' => $bina,
                            'kontroller' => []
                        ];
                    }

                    $bugunYapilacakKontroller[$bina->id]['kontroller'][] = $kontrolMaddesi;
                }
            }
        }

        return view('personel.dashboard', compact('bugunYapilacakKontroller'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kontrol_maddesi_id' => 'required|exists:kontrol_maddeleri,id',
            'girilen_deger' => 'required',
        ]);

        try {
            DB::beginTransaction();

            // Bugün için aynı kontrolün kaydı var mı kontrol et
            $mevcutKayit = KontrolKaydi::where('kontrol_maddesi_id', $validated['kontrol_maddesi_id'])
                ->whereDate('tarih', Carbon::today())
                ->first();

            if ($mevcutKayit) {
                return back()->with('error', 'Bu kontrol bugün zaten yapılmış!');
            }

            KontrolKaydi::create([
                'kontrol_maddesi_id' => $validated['kontrol_maddesi_id'],
                'tarih' => Carbon::today(),
                'girilen_deger' => $validated['girilen_deger'],
                'yapan_kullanici_id' => Auth::id(),
            ]);

            DB::commit();

            return back()->with('success', 'Kontrol başarıyla kaydedildi!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bir hata oluştu: ' . $e->getMessage());
        }
    }
}
