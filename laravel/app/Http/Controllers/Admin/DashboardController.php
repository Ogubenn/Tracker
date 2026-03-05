<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bina;
use App\Models\KontrolMaddesi;
use App\Models\KontrolKaydi;
use App\Models\User;
use App\Models\DashboardNote;
use App\Models\IsTakvimi;
use App\Models\LaboratuvarRapor;
use App\Models\BinaCalismaDurumu;
use App\Notifications\DashboardNoteNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        return view('admin.dashboard', [
            'binaSayisi' => $this->getAktifBinaSayisi(),
            'kontrolMaddesiSayisi' => $this->getAktifKontrolMaddesiSayisi(),
            'personelSayisi' => $this->getAktifPersonelSayisi(),
            'bugunYapilanKontroller' => $this->getBugunYapilanKontrolSayisi(),
            'latestNotes' => $this->getLatestNotes(),
            'calendar' => $this->getCalendarData($currentYear, $currentMonth),
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'today' => $today->day,
            'bugunFotograflar' => $this->getBugunFotograflar(),
            'bugunIsler' => $this->getBugunIsler(),
            'laboratuvarStats' => $this->getLaboratuvarStats(),
            'dekantorCalismaOrani' => $this->getDekantorCalismaOrani($currentYear, $currentMonth),
            'aylikTamamlanmaOrani' => $this->getAylikTamamlanmaOrani($currentYear, $currentMonth),
            'girisDebisi' => $this->getGirisDebisi(),
            'cikisDebisi' => $this->getCikisDebisi(),
            'geriDevirDebisi' => $this->getGeriDevirDebisi(),
        ]);
    }

    public function storeNote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $note = DashboardNote::create([
            'user_id' => auth()->id(),
            'note' => $validated['note'],
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Not başarıyla kaydedildi.');
    }

    public function sendNoteToUsers(Request $request, int $noteId): RedirectResponse
    {
        $note = DashboardNote::findOrFail($noteId);
        
        $users = User::where('aktif_mi', true)
            ->where('mail_alsin', true)
            ->get();

        if ($users->isEmpty()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Mail alacak aktif kullanıcı bulunamadı.');
        }

        $senderName = auth()->user()->ad;

        foreach ($users as $user) {
            $user->notify(new DashboardNoteNotification($note->note, $senderName));
        }

        $note->update(['mail_sent_at' => now()]);

        return redirect()->route('admin.dashboard')
            ->with('success', $users->count() . ' kullanıcıya mail gönderildi.');
    }

    public function deleteNote(int $noteId): RedirectResponse
    {
        $note = DashboardNote::findOrFail($noteId);
        $note->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Not silindi.');
    }

    private function getAktifBinaSayisi(): int
    {
        return Bina::where('aktif_mi', true)->count();
    }

    private function getAktifKontrolMaddesiSayisi(): int
    {
        return KontrolMaddesi::where('aktif_mi', true)->count();
    }

    private function getAktifPersonelSayisi(): int
    {
        return User::where('rol', 'personel')
            ->where('aktif_mi', true)
            ->count();
    }

    private function getBugunYapilanKontrolSayisi(): int
    {
        return KontrolKaydi::whereDate('tarih', Carbon::today())->count();
    }

    private function getDekantorCalismaOrani(int $year, int $month): array
    {
        // Ayın toplam gün sayısı
        $ayinGunSayisi = Carbon::create($year, $month, 1)->daysInMonth;

        // Dekantör binası bitiş saati kontrol maddesi (ID: 37)
        // Bu kontrol maddesine veri girilen gün sayısı = Dekantör çalışan gün sayısı
        $calisanGun = KontrolKaydi::where('kontrol_maddesi_id', 37)
            ->whereYear('tarih', $year)
            ->whereMonth('tarih', $month)
            ->whereNotNull('girilen_deger')
            ->pluck('tarih')
            ->map(function($tarih) {
                return $tarih instanceof \Carbon\Carbon ? $tarih->format('Y-m-d') : $tarih;
            })
            ->unique()
            ->count();
        
        // Çalışma oranı hesapla (çalışan gün / ayın toplam gün sayısı)
        $calismaOrani = $ayinGunSayisi > 0 
            ? round(($calisanGun / $ayinGunSayisi) * 100, 1)
            : 0;

        return [
            'oran' => $calismaOrani,
            'calisan_gun' => $calisanGun,
            'toplam_gun' => $ayinGunSayisi,
            'bina_adi' => 'Dekantör',
        ];
    }

    private function getAylikTamamlanmaOrani(int $year, int $month): array
    {
        $calendar = $this->getCalendarData($year, $month);
        
        // Ayın toplam gün sayısını al
        $ayinGunSayisi = Carbon::create($year, $month, 1)->daysInMonth;
        
        $yesilGun = 0;
        $sariGun = 0;
        $kirmiziGun = 0;
        $calisilmisGun = 0;
        
        foreach ($calendar['days'] as $dayData) {
            // Sadece geçmiş ve bugünü say
            if ($dayData['status'] !== 'future') {
                // future ve none dışındaki günleri say (çalışılan günler)
                if ($dayData['status'] !== 'none') {
                    $calisilmisGun++;
                    
                    if ($dayData['status'] === 'success') {
                        $yesilGun++;
                    } elseif ($dayData['status'] === 'warning') {
                        $sariGun++;
                    } elseif ($dayData['status'] === 'danger') {
                        $kirmiziGun++;
                    }
                }
            }
        }
        
        // Tamamlanma oranı hesapla (yeşil günler / ayın toplam gün sayısı)
        $tamamlanmaOrani = $ayinGunSayisi > 0 
            ? round(($yesilGun / $ayinGunSayisi) * 100, 1)
            : 0;
        
        return [
            'oran' => $tamamlanmaOrani,
            'yesil_gun' => $yesilGun,
            'sari_gun' => $sariGun,
            'kirmizi_gun' => $kirmiziGun,
            'toplam_gun' => $ayinGunSayisi,
            'calisilmis_gun' => $calisilmisGun,
        ];
    }

    private function getLatestNotes()
    {
        return DashboardNote::with('user')
            ->latest()
            ->take(5)
            ->get();
    }

    private function getDebimetreFark(int $kontrolMaddesiId): ?float
    {
        $bugun = Carbon::today();
        $dun = Carbon::yesterday();
        
        // Bugünkü değer
        $bugunKayit = KontrolKaydi::where('kontrol_maddesi_id', $kontrolMaddesiId)
            ->whereDate('tarih', $bugun)
            ->whereNotNull('girilen_deger')
            ->first();
        
        // Dünkü değer
        $dunKayit = KontrolKaydi::where('kontrol_maddesi_id', $kontrolMaddesiId)
            ->whereDate('tarih', $dun)
            ->whereNotNull('girilen_deger')
            ->first();
        
        if (!$bugunKayit || !$dunKayit) {
            return null; // Veri eksikse null döner
        }
        
        $fark = floatval($bugunKayit->girilen_deger) - floatval($dunKayit->girilen_deger);
        
        return $fark;
    }

    private function getGirisDebisi(): array
    {
        $fark = $this->getDebimetreFark(54);
        
        return [
            'deger' => $fark,
            'format' => $fark !== null ? number_format($fark, 0, ',', '.') : '-',
        ];
    }

    private function getCikisDebisi(): array
    {
        $fark = $this->getDebimetreFark(55);
        
        return [
            'deger' => $fark,
            'format' => $fark !== null ? number_format($fark, 0, ',', '.') : '-',
        ];
    }

    private function getGeriDevirDebisi(): array
    {
        $fark = $this->getDebimetreFark(73);
        
        return [
            'deger' => $fark,
            'format' => $fark !== null ? number_format($fark, 0, ',', '.') : '-',
        ];
    }

    private function getBugunFotograflar()
    {
        try {
            $bugun = Carbon::today();
            
            $kayitlar = KontrolKaydi::with(['bina', 'kontrolMaddesi'])
                ->whereDate('tarih', $bugun)
                ->whereNotNull('fotograflar')
                ->get();

            $fotograflar = [];
            
            foreach ($kayitlar as $kayit) {
                if ($kayit->hasFotograflar()) {
                    foreach ($kayit->fotograflar as $foto) {
                        if (!empty($foto)) {
                            $fotograflar[] = [
                                'path' => $foto,
                                'url' => \Storage::disk('public')->url($foto),
                                'bina' => $kayit->bina ? $kayit->bina->bina_adi : 'Bilinmiyor',
                                'madde' => $kayit->kontrolMaddesi ? $kayit->kontrolMaddesi->kontrol_adi : 'Bilinmiyor',
                                'kayit_id' => $kayit->id,
                            ];
                        }
                    }
                }
            }

            return collect($fotograflar)->take(12);
        } catch (\Exception $e) {
            \Log::error('Dashboard fotoğraf hatası: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function getCalendarData(int $year, int $month): array
    {
        $firstDay = Carbon::create($year, $month, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDayOfWeek = $firstDay->dayOfWeek; // 0=Pazar, 1=Pazartesi, ...
        
        $calendar = [];
        
        // Ay içindeki her gün için kontrol durumunu hesapla
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            
            // Bu gün için çalışmayan binaları kontrol et
            $calismayanBinalar = BinaCalismaDurumu::calismayanBinalar($date);
            $calismayanBinaAdlari = [];
            if (!empty($calismayanBinalar)) {
                $calismayanBinaAdlari = Bina::whereIn('id', $calismayanBinalar)
                    ->pluck('bina_adi')
                    ->toArray();
            }
            
            $calendar[] = [
                'day' => $day,
                'date' => $date,
                'status' => $this->getDayStatus($date),
                'calismayan_binalar' => $calismayanBinaAdlari,
            ];
        }

        return [
            'days' => $calendar,
            'startDayOfWeek' => $startDayOfWeek,
            'monthName' => $firstDay->translatedFormat('F Y'),
        ];
    }

    private function getDayStatus(Carbon $date): string
    {
        $today = Carbon::today();
        
        // Gelecek günler için status yok
        if ($date->isFuture()) {
            return 'future';
        }

        // Bugünse ve kontroller devam ediyorsa
        if ($date->isToday()) {
            $bugunYapilmasi = $this->getBugunYapilmasiGerekenKontrolSayisi($date);
            $bugunYapilan = $this->getGunYapilanKontrolSayisi($date);
            
            if ($bugunYapilmasi == 0) {
                return 'none'; // Yapılacak kontrol yok
            }
            
            if ($bugunYapilan == 0) {
                return 'danger'; // Hiç yapılmamış
            }
            
            if ($bugunYapilan < $bugunYapilmasi) {
                return 'warning'; // Devam ediyor
            }
            
            // Hepsi yapılmış, uygunsuz var mı kontrol et
            $uygunsuzVarMi = $this->hasUygunsuzKontrol($date);
            return $uygunsuzVarMi ? 'danger' : 'success';
        }

        // Geçmiş günler için
        $yapilmasi = $this->getBugunYapilmasiGerekenKontrolSayisi($date);
        $yapilan = $this->getGunYapilanKontrolSayisi($date);
        
        if ($yapilmasi == 0) {
            return 'none'; // Yapılacak kontrol yoktu
        }
        
        if ($yapilan == 0) {
            return 'danger'; // Hiç yapılmamış
        }
        
        if ($yapilan < $yapilmasi) {
            return 'danger'; // Eksik
        }
        
        // Hepsi yapılmış, uygunsuz var mı kontrol et
        $uygunsuzVarMi = $this->hasUygunsuzKontrol($date);
        return $uygunsuzVarMi ? 'danger' : 'success';
    }

    private function getGunYapilanKontrolSayisi(Carbon $date): int
    {
        return KontrolKaydi::whereDate('tarih', $date)->count();
    }

    private function getBugunYapilmasiGerekenKontrolSayisi(Carbon $date): int
    {
        // Çalışmayan binaları filtrele
        $calismayanBinalar = BinaCalismaDurumu::calismayanBinalar($date);
        
        $binalar = Bina::where('aktif_mi', true)
            ->whereNotIn('id', $calismayanBinalar) // Çalışmayan binaları hariç tut
            ->with(['kontrolMaddeleri' => fn($q) => $q->where('aktif_mi', true)])
            ->get();

        $count = 0;
        foreach ($binalar as $bina) {
            foreach ($bina->kontrolMaddeleri as $madde) {
                if ($this->kontrolBugunYapilmaliMi($madde, $date)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    private function kontrolBugunYapilmaliMi($madde, Carbon $date): bool
    {
        if ($madde->periyot === 'gunluk') {
            return true;
        }

        if ($madde->periyot === 'haftalik' && $madde->haftalik_gun) {
            $gunMap = [
                'pazartesi' => 1, 'sali' => 2, 'carsamba' => 3,
                'persembe' => 4, 'cuma' => 5, 'cumartesi' => 6, 'pazar' => 0
            ];
            return $date->dayOfWeek == ($gunMap[$madde->haftalik_gun] ?? -1);
        }

        // 15 günlük ve aylık kontroller için basitleştirilmiş mantık
        return false; // Şimdilik sadece günlük ve haftalık
    }

    private function hasUygunsuzKontrol(Carbon $date): bool
    {
        return KontrolKaydi::whereDate('tarih', $date)
            ->whereIn('durum', ['uygun_degil', 'duzeltme_gerekli'])
            ->exists();
    }

    public function getDayDetails(Request $request)
    {
        $date = Carbon::parse($request->date);
        
        // Çalışmayan binaları filtrele
        $calismayanBinalar = BinaCalismaDurumu::calismayanBinalar($date);
        
        // Çalışmayan bina bilgilerini al (modal'da göstermek için)
        $calismayanBinaDetaylari = [];
        if (!empty($calismayanBinalar)) {
            $calismayanBinaDetaylari = Bina::whereIn('id', $calismayanBinalar)
                ->pluck('bina_adi')
                ->toArray();
        }
        
        $binalar = Bina::where('aktif_mi', true)
            ->whereNotIn('id', $calismayanBinalar) // Çalışmayan binaları hariç tut
            ->with(['kontrolMaddeleri' => fn($q) => $q->where('aktif_mi', true)])
            ->get();

        $yapilmasiGerekenler = [];
        $yapilan = [];
        $uygunsuzlar = [];

        foreach ($binalar as $bina) {
            foreach ($bina->kontrolMaddeleri as $madde) {
                if ($this->kontrolBugunYapilmaliMi($madde, $date)) {
                    $kontrolKey = $bina->bina_adi . ' - ' . $madde->kontrol_adi;
                    $yapilmasiGerekenler[] = $kontrolKey;
                    
                    $kontrol = KontrolKaydi::where('bina_id', $bina->id)
                        ->where('kontrol_maddesi_id', $madde->id)
                        ->whereDate('tarih', $date)
                        ->first();
                    
                    if ($kontrol) {
                        $yapilan[] = $kontrolKey;
                        if (in_array($kontrol->durum, ['uygun_degil', 'duzeltme_gerekli'])) {
                            $uygunsuzlar[] = $kontrolKey . ' (' . $kontrol->durum . ')';
                        }
                    }
                }
            }
        }

        $eksikler = array_diff($yapilmasiGerekenler, $yapilan);

        return response()->json([
            'date' => $date->translatedFormat('d F Y'),
            'yapilmasi_gereken' => count($yapilmasiGerekenler),
            'yapilan' => count($yapilan),
            'eksik' => count($eksikler),
            'eksik_kontroller' => array_values($eksikler),
            'uygunsuz_kontroller' => $uygunsuzlar,
            'calismayan_binalar' => $calismayanBinaDetaylari,
        ]);
    }
    
    /**
     * Bugünkü işleri getir (İş Takvimi)
     */
    private function getBugunIsler()
    {
        // Gecikenleri güncelle
        IsTakvimi::gecikenleriGuncelle();
        
        try {
            return IsTakvimi::with(['atananKullanici', 'atananKullanicilar'])
                ->bugun()
                ->orderBy('durum', 'asc')
                ->get();
        } catch (\Exception $e) {
            // Pivot tablo henüz yoksa sadece tek kullanıcı ile çalış
            return IsTakvimi::with('atananKullanici')
                ->bugun()
                ->orderBy('durum', 'asc')
                ->get();
        }
    }

    /**
     * Laboratuvar istatistiklerini getir
     */
    private function getLaboratuvarStats()
    {
        try {
            $toplamRapor = LaboratuvarRapor::count();
            $buAyRapor = LaboratuvarRapor::whereMonth('rapor_tarihi', Carbon::now()->month)
                ->whereYear('rapor_tarihi', Carbon::now()->year)
                ->count();
            
            $sonRaporlar = LaboratuvarRapor::with(['parametreler', 'olusturan'])
                ->latest('rapor_tarihi')
                ->take(5)
                ->get();
            
            // Uygunluk durumları
            $toplamParametre = \DB::table('laboratuvar_parametreler')->count();
            $uygunParametre = \DB::table('laboratuvar_parametreler')
                ->where('uygunluk', 'uygun')
                ->count();
            $uygunDegilParametre = \DB::table('laboratuvar_parametreler')
                ->where('uygunluk', 'uygun_degil')
                ->count();
            
            return [
                'toplam_rapor' => $toplamRapor,
                'bu_ay_rapor' => $buAyRapor,
                'son_raporlar' => $sonRaporlar,
                'toplam_parametre' => $toplamParametre,
                'uygun_parametre' => $uygunParametre,
                'uygun_degil_parametre' => $uygunDegilParametre,
                'uygunluk_yuzdesi' => $toplamParametre > 0 ? round(($uygunParametre / $toplamParametre) * 100, 1) : 0,
            ];
        } catch (\Exception $e) {
            \Log::error('Laboratuvar stats hatası: ' . $e->getMessage());
            return [
                'toplam_rapor' => 0,
                'bu_ay_rapor' => 0,
                'son_raporlar' => collect(),
                'toplam_parametre' => 0,
                'uygun_parametre' => 0,
                'uygun_degil_parametre' => 0,
                'uygunluk_yuzdesi' => 0,
            ];
        }
    }
}
