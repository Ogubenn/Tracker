<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KontrolKaydi;
use App\Models\Bina;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RaporController extends Controller
{
    public function index(Request $request)
    {
        $binalar = Bina::aktif()->get();
        $tarih = $request->get('tarih', Carbon::today()->format('Y-m-d'));
        $binaId = $request->get('bina_id');

        $kayitlar = null;

        if ($binaId) {
            $query = KontrolKaydi::with([
                    'kontrolMaddesi',
                    'bina',
                    'yapanKullanici'
                ])
                ->whereDate('tarih', $tarih);
            
            // Eğer "all" seçilmemişse sadece o binayı filtrele
            if ($binaId !== 'all') {
                $query->where('bina_id', $binaId);
            }
            
            $kayitlar = $query->get()->groupBy('bina.bina_adi');
        }

        return view('admin.raporlar.index', compact('binalar', 'tarih', 'binaId', 'kayitlar'));
    }
}
