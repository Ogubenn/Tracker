<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bina;
use App\Models\KontrolKaydi;
use App\Models\KontrolMaddesi;
use App\Models\User;
use App\Services\FileService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GecmisTarihKontrolController extends Controller
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function index(Request $request)
    {
        try {
            $binalar = Bina::orderBy('bina_adi')->get();
            $personeller = User::where('aktif_mi', true)
                ->orderBy('ad')
                ->get();

            $kontrolMaddeleri = null;
            $seciliBina = null;

            // Eğer filtre uygulanmışsa kontrol maddelerini getir
            if ($request->filled(['tarih', 'bina_id', 'personel_id'])) {
                $tarih = Carbon::parse($request->tarih);
                $seciliBina = Bina::findOrFail($request->bina_id);
                
                // O tarihte yapılması gereken ama yapılmamış kontrolleri getir
                $kontrolMaddeleri = $this->getRequiredKontrollerForDate($seciliBina, $tarih);
            }

            return view('admin.kontroller.gecmis-tarih', compact(
                'binalar',
                'personeller',
                'kontrolMaddeleri',
                'seciliBina'
            ));
        } catch (\Exception $e) {
            \Log::error('Geçmiş tarih kontrol sayfası hatası: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tarih' => 'required|date|before_or_equal:today',
            'bina_id' => 'required|exists:binalar,id',
            'personel_id' => 'required|exists:users,id',
            'kayitlar' => 'nullable|array',
            'kayitlar.*.kontrol_maddesi_id' => 'required|exists:kontrol_maddeleri,id',
            'kayitlar.*.girilen_deger' => 'nullable|string',
            'kayitlar.*.baslangic_saati' => 'nullable|date_format:H:i',
            'kayitlar.*.bitis_saati' => 'nullable|date_format:H:i',
            'kayitlar.*.durum' => 'nullable|in:uygun,uygun_degil,duzeltme_gerekli',
            'kayitlar.*.fotograflar.*' => 'nullable|file|image|mimes:jpeg,jpg,png,gif,webp|max:20480',
            'genel_aciklama' => 'nullable|string',
        ]);

        try {
            $tarih = Carbon::parse($validated['tarih']);
            $kaydedilenSayisi = 0;

            if (isset($validated['kayitlar']) && !empty($validated['kayitlar'])) {
                foreach ($validated['kayitlar'] as $index => $kayit) {
                    // Durum seçilmemişse bu kaydı atla
                    if (empty($kayit['durum'])) {
                        continue;
                    }

                    // Fotoğrafları yükle
                    $kontrolFotograflar = [];
                    if ($request->hasFile("kayitlar.{$index}.fotograflar")) {
                        $kontrolFotograflar = $this->fileService->uploadPhotos(
                            $request->file("kayitlar.{$index}.fotograflar")
                        );
                    }

                    KontrolKaydi::create([
                        'bina_id' => $validated['bina_id'],
                        'kontrol_maddesi_id' => $kayit['kontrol_maddesi_id'],
                        'tarih' => $tarih,
                        'girilen_deger' => $kayit['girilen_deger'] ?? null,
                        'baslangic_saati' => $kayit['baslangic_saati'] ?? null,
                        'bitis_saati' => $kayit['bitis_saati'] ?? null,
                        'yapan_kullanici_id' => $validated['personel_id'],
                        'durum' => $kayit['durum'],
                        'aciklama' => $validated['genel_aciklama'] ?? null,
                        'onay_durumu' => 'onaylandi', // Geçmiş tarihli kayıtlar direkt onaylı
                        'onaylayan_id' => auth()->id(),
                        'onay_tarihi' => now(),
                        'ip_adresi' => $request->ip(),
                        'fotograflar' => $kontrolFotograflar,
                    ]);

                    $kaydedilenSayisi++;
                }
            }

            if ($kaydedilenSayisi > 0) {
                return redirect()
                    ->route('admin.kontroller.gecmis-tarih', [
                        'tarih' => $request->tarih,
                        'bina_id' => $request->bina_id,
                        'personel_id' => $request->personel_id
                    ])
                    ->with('success', "✅ {$kaydedilenSayisi} kontrol kaydı başarıyla oluşturuldu.");
            } else {
                return redirect()
                    ->back()
                    ->with('error', 'Hiçbir kontrol kaydedilmedi. Lütfen en az bir durum seçin.')
                    ->withInput();
            }

        } catch (\Exception $e) {
            \Log::error('Geçmiş tarihli kontrol kaydetme hatası: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Hata: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function getRequiredKontrollerForDate(Bina $bina, Carbon $tarih)
    {
        return KontrolMaddesi::where('bina_id', $bina->id)
            ->where('aktif_mi', true)
            ->get()
            ->filter(function($madde) use ($bina, $tarih) {
                // O tarihte bu kontrol zaten yapılmış mı?
                $yapilmisMi = KontrolKaydi::where('bina_id', $bina->id)
                    ->where('kontrol_maddesi_id', $madde->id)
                    ->whereDate('tarih', $tarih)
                    ->exists();
                
                if ($yapilmisMi) {
                    return false;
                }
                
                // Periyot kontrolü - o tarihte yapılması gerekiyor muydu?
                return $this->wasKontrolRequiredOnDate($madde, $bina, $tarih);
            });
    }

    private function wasKontrolRequiredOnDate(KontrolMaddesi $madde, Bina $bina, Carbon $tarih): bool
    {
        switch($madde->periyot) {
            case 'gunluk':
                return true;
                
            case 'haftalik':
                return $this->wasPeriyotDueOnDate($madde, $bina, $tarih, 7);
                
            case '15_gun':
                return $this->wasPeriyotDueOnDate($madde, $bina, $tarih, 15);
                
            case 'aylik':
                return $this->wasPeriyotDueOnDateInMonths($madde, $bina, $tarih, 1);
                
            default:
                return false;
        }
    }

    private function wasPeriyotDueOnDate(KontrolMaddesi $madde, Bina $bina, Carbon $tarih, int $days): bool
    {
        // O tarihten önceki son kaydı bul
        $sonKayit = KontrolKaydi::where('bina_id', $bina->id)
            ->where('kontrol_maddesi_id', $madde->id)
            ->where('tarih', '<', $tarih)
            ->latest('tarih')
            ->first();
        
        // Hiç kayıt yoksa veya son kayıt yeterince eski ise
        return !$sonKayit || $sonKayit->tarih->diffInDays($tarih) >= $days;
    }

    private function wasPeriyotDueOnDateInMonths(KontrolMaddesi $madde, Bina $bina, Carbon $tarih, int $months): bool
    {
        $sonKayit = KontrolKaydi::where('bina_id', $bina->id)
            ->where('kontrol_maddesi_id', $madde->id)
            ->where('tarih', '<', $tarih)
            ->latest('tarih')
            ->first();
        
        return !$sonKayit || $sonKayit->tarih->diffInMonths($tarih) >= $months;
    }
}
