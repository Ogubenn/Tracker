<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PersonelDevam;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PersonelDevamController extends Controller
{
    public function index(Request $request)
    {
        // Varsayılan olarak bu hafta
        $tarih = $request->filled('tarih') 
            ? Carbon::parse($request->tarih) 
            : Carbon::now();
        
        // Haftanın başlangıcı (Pazartesi)
        $haftaBaslangic = $tarih->copy()->startOfWeek(Carbon::MONDAY);
        $haftaBitis = $tarih->copy()->endOfWeek(Carbon::SUNDAY);
        
        // Önceki ve sonraki hafta
        $oncekiHafta = $haftaBaslangic->copy()->subWeek();
        $sonrakiHafta = $haftaBaslangic->copy()->addWeek();
        
        // Tüm personeli getir
        $personeller = User::where('aktif_mi', true)
            ->orderBy('ad')
            ->get();
        
        // 7 günlük array oluştur
        $gunler = [];
        for ($i = 0; $i < 7; $i++) {
            $gunler[] = $haftaBaslangic->copy()->addDays($i);
        }
        
        // Bu hafta için tüm devam kayıtlarını getir
        $devamKayitlari = PersonelDevam::with('user')
            ->tarihAralik($haftaBaslangic, $haftaBitis)
            ->get()
            ->keyBy(function($item) {
                return $item->user_id . '_' . $item->tarih->format('Y-m-d');
            });
        
        return view('admin.personel-devam.index', compact(
            'personeller',
            'gunler',
            'devamKayitlari',
            'haftaBaslangic',
            'oncekiHafta',
            'sonrakiHafta'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kayitlar' => 'required|array',
            'kayitlar.*.user_id' => 'required|exists:users,id',
            'kayitlar.*.tarih' => 'required|date',
            'kayitlar.*.giris_yapti' => 'nullable|boolean',
            'kayitlar.*.cikis_yapti' => 'nullable|boolean',
            'kayitlar.*.durum' => 'required|in:calisma,izinli,raporlu,gelmedi',
            'kayitlar.*.notlar' => 'nullable|string',
        ]);

        try {
            $kaydedilenSayisi = 0;
            
            foreach ($validated['kayitlar'] as $kayit) {
                PersonelDevam::updateOrCreate(
                    [
                        'user_id' => $kayit['user_id'],
                        'tarih' => $kayit['tarih'],
                    ],
                    [
                        'giris_yapti' => $kayit['giris_yapti'] ?? false,
                        'cikis_yapti' => $kayit['cikis_yapti'] ?? false,
                        'durum' => $kayit['durum'],
                        'notlar' => $kayit['notlar'] ?? null,
                        'kaydeden_id' => auth()->id(),
                    ]
                );
                
                $kaydedilenSayisi++;
            }

            return redirect()
                ->back()
                ->with('success', "✅ {$kaydedilenSayisi} devam kaydı başarıyla güncellendi.");

        } catch (\Exception $e) {
            \Log::error('Personel devam kaydetme hatası: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Hata: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function aylikGoruntule(Request $request)
    {
        $yil = $request->filled('yil') ? $request->yil : Carbon::now()->year;
        $ay = $request->filled('ay') ? $request->ay : Carbon::now()->month;
        
        $tarih = Carbon::create($yil, $ay, 1);
        $ayBaslangic = $tarih->copy()->startOfMonth();
        $ayBitis = $tarih->copy()->endOfMonth();
        
        // Önceki ve sonraki ay
        $oncekiAy = $tarih->copy()->subMonth();
        $sonrakiAy = $tarih->copy()->addMonth();
        
        // Tüm personeli getir
        $personeller = User::where('aktif_mi', true)
            ->orderBy('ad')
            ->get();
        
        // Aydaki tüm günleri oluştur
        $gunler = [];
        $gunSayisi = $ayBitis->day;
        for ($i = 1; $i <= $gunSayisi; $i++) {
            $gunler[] = Carbon::create($yil, $ay, $i);
        }
        
        // Bu ay için tüm devam kayıtlarını getir
        $devamKayitlari = PersonelDevam::with('user')
            ->tarihAralik($ayBaslangic, $ayBitis)
            ->get()
            ->keyBy(function($item) {
                return $item->user_id . '_' . $item->tarih->format('Y-m-d');
            });
        
        // Her personel için istatistik hesapla
        $istatistikler = [];
        foreach ($personeller as $personel) {
            $toplamCalisma = 0;
            $toplamIzin = 0;
            $toplamRapor = 0;
            $toplamGelmedi = 0;
            
            foreach ($gunler as $gun) {
                $key = $personel->id . '_' . $gun->format('Y-m-d');
                if (isset($devamKayitlari[$key])) {
                    $kayit = $devamKayitlari[$key];
                    switch ($kayit->durum) {
                        case 'calisma': $toplamCalisma++; break;
                        case 'izinli': $toplamIzin++; break;
                        case 'raporlu': $toplamRapor++; break;
                        case 'gelmedi': $toplamGelmedi++; break;
                    }
                }
            }
            
            $istatistikler[$personel->id] = [
                'calisma' => $toplamCalisma,
                'izin' => $toplamIzin,
                'rapor' => $toplamRapor,
                'gelmedi' => $toplamGelmedi,
            ];
        }
        
        return view('admin.personel-devam.aylik', compact(
            'personeller',
            'gunler',
            'devamKayitlari',
            'istatistikler',
            'tarih',
            'oncekiAy',
            'sonrakiAy'
        ));
    }

    public function pdfIndir(Request $request)
    {
        $yil = $request->filled('yil') ? $request->yil : Carbon::now()->year;
        $ay = $request->filled('ay') ? $request->ay : Carbon::now()->month;
        
        $tarih = Carbon::create($yil, $ay, 1);
        $ayBaslangic = $tarih->copy()->startOfMonth();
        $ayBitis = $tarih->copy()->endOfMonth();
        
        // Tüm personeli getir
        $personeller = User::where('aktif_mi', true)
            ->orderBy('ad')
            ->get();
        
        // Aydaki tüm günleri oluştur
        $gunler = [];
        $gunSayisi = $ayBitis->day;
        for ($i = 1; $i <= $gunSayisi; $i++) {
            $gunler[] = Carbon::create($yil, $ay, $i);
        }
        
        // Bu ay için tüm devam kayıtlarını getir
        $devamKayitlari = PersonelDevam::with('user')
            ->tarihAralik($ayBaslangic, $ayBitis)
            ->get()
            ->keyBy(function($item) {
                return $item->user_id . '_' . $item->tarih->format('Y-m-d');
            });
        
        // İstatistikler
        $istatistikler = [];
        foreach ($personeller as $personel) {
            $toplamCalisma = 0;
            $toplamIzin = 0;
            $toplamRapor = 0;
            $toplamGelmedi = 0;
            
            foreach ($gunler as $gun) {
                $key = $personel->id . '_' . $gun->format('Y-m-d');
                if (isset($devamKayitlari[$key])) {
                    $kayit = $devamKayitlari[$key];
                    switch ($kayit->durum) {
                        case 'calisma': $toplamCalisma++; break;
                        case 'izinli': $toplamIzin++; break;
                        case 'raporlu': $toplamRapor++; break;
                        case 'gelmedi': $toplamGelmedi++; break;
                    }
                }
            }
            
            $istatistikler[$personel->id] = [
                'calisma' => $toplamCalisma,
                'izin' => $toplamIzin,
                'rapor' => $toplamRapor,
                'gelmedi' => $toplamGelmedi,
            ];
        }
        
        $pdf = Pdf::loadView('admin.personel-devam.pdf', compact(
            'personeller',
            'gunler',
            'devamKayitlari',
            'istatistikler',
            'tarih'
        ));
        
        $pdf->setPaper('a4', 'landscape');
        
        $dosyaAdi = 'personel-devam-' . $tarih->format('Y-m') . '.pdf';
        
        return $pdf->download($dosyaAdi);
    }
}
