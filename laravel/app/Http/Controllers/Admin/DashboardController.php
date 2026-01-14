<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bina;
use App\Models\KontrolMaddesi;
use App\Models\KontrolKaydi;
use App\Models\User;
use App\Models\DashboardNote;
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

    private function getLatestNotes()
    {
        return DashboardNote::with('user')
            ->latest()
            ->take(5)
            ->get();
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
            $calendar[] = [
                'day' => $day,
                'date' => $date,
                'status' => $this->getDayStatus($date),
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
        $binalar = Bina::where('aktif_mi', true)
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
        
        $binalar = Bina::where('aktif_mi', true)
            ->with(['kontrolMaddeleri' => fn($q) => $q->where('aktif_mi', true)])
            ->get();

        $yapilmasiGerekenler = [];
        $yapilan = [];
        $uygunsuzlar = [];

        foreach ($binalar as $bina) {
            foreach ($bina->kontrolMaddeleri as $madde) {
                if ($this->kontrolBugunYapilmaliMi($madde, $date)) {
                    $kontrolKey = $bina->ad . ' - ' . $madde->kontrol_adi;
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
        ]);
    }
}
