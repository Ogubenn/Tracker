<?php

namespace App\Http\Controllers;

use App\Models\Bina;
use App\Models\KontrolKaydi;
use App\Models\KontrolMaddesi;
use App\Models\User;
use App\Services\FileService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicKontrolController extends Controller
{
    private const PERIYOT_GUNLUK = 'gunluk';
    private const PERIYOT_HAFTALIK = 'haftalik';
    private const PERIYOT_15_GUN = '15_gun';
    private const PERIYOT_AYLIK = 'aylik';
    
    private const MAX_FILE_SIZE = 20480; // 20MB

    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function index(string $uuid)
    {
        $bina = $this->findBinaByUuid($uuid);
        $personeller = $this->getVisibleUsers();
        $bugun = Carbon::today();
        $kontrolMaddeleri = $this->getRequiredKontrollerForToday($bina, $bugun);
        
        return view('public.kontrol', compact('bina', 'kontrolMaddeleri', 'personeller', 'bugun'));
    }
    
    public function store(Request $request, string $uuid)
    {
        $bina = $this->findBinaByUuid($uuid);
        
        try {
            $validated = $this->validateKontrolData($request);
            
            // DEBUG: Fotoğraf yükleme kontrolü
            if ($request->hasFile('genel_dosyalar')) {
                \Log::info('Genel dosyalar var: ' . count($request->file('genel_dosyalar')));
            }
            
            $genelDosyalar = $this->uploadFiles($request, $bina);
            \Log::info('Yüklenen fotoğraf sayısı: ' . count($genelDosyalar));
            
            $this->saveKontrolKayitlari($validated, $bina, $genelDosyalar, $request->ip(), $request);
            
            return redirect()->back()->with('success', 'Kontrol kayıtları başarıyla oluşturuldu. Admin onayı bekleniyor.');
            
        } catch (\Exception $e) {
            \Log::error('Kontrol kaydetme hatası: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Hata: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    private function findBinaByUuid(string $uuid): Bina
    {
        return Bina::where('uuid', $uuid)->firstOrFail();
    }

    private function getVisibleUsers()
    {
        return User::where('aktif_mi', true)
            ->where('qr_gorunur', true)
            ->orderBy('rol', 'asc')
            ->orderBy('ad', 'asc')
            ->get();
    }

    private function getRequiredKontrollerForToday(Bina $bina, Carbon $bugun)
    {
        return KontrolMaddesi::where('bina_id', $bina->id)
            ->where('aktif_mi', true)
            ->get()
            ->filter(fn($madde) => $this->kontrolGerekliMi($madde, $bina, $bugun));
    }

    private function validateKontrolData(Request $request): array
    {
        return $request->validate([
            'personel_id' => 'required|exists:users,id',
            'kayitlar' => 'required|array',
            'kayitlar.*.kontrol_maddesi_id' => 'required|exists:kontrol_maddeleri,id',
            'kayitlar.*.girilen_deger' => 'nullable|string',
            'kayitlar.*.baslangic_saati' => 'nullable|date_format:H:i',
            'kayitlar.*.bitis_saati' => 'nullable|date_format:H:i',
            'kayitlar.*.durum' => 'required|in:uygun,uygun_degil,duzeltme_gerekli',
            'kayitlar.*.fotograflar.*' => 'nullable|file|image|mimes:jpeg,jpg,png,gif,webp|max:' . self::MAX_FILE_SIZE,
            'genel_aciklama' => 'nullable|string',
            'genel_dosyalar.*' => 'nullable|file|image|mimes:jpeg,jpg,png,gif,webp|max:' . self::MAX_FILE_SIZE,
        ]);
    }

    private function uploadFiles(Request $request, Bina $bina): array
    {
        if (!$request->hasFile('genel_dosyalar')) {
            return [];
        }

        return $this->fileService->uploadPhotos($request->file('genel_dosyalar'));
    }

    private function saveKontrolKayitlari(array $validated, Bina $bina, array $genelDosyalar, string $ipAddress, Request $request): void
    {
        $tarih = Carbon::today();

        foreach ($validated['kayitlar'] as $index => $kayit) {
            // Her kontrol için kendi fotoğraflarını yükle
            $kontrolFotograflar = [];
            if ($request->hasFile("kayitlar.{$index}.fotograflar")) {
                $kontrolFotograflar = $this->fileService->uploadPhotos(
                    $request->file("kayitlar.{$index}.fotograflar")
                );
            }

            KontrolKaydi::create([
                'bina_id' => $bina->id,
                'kontrol_maddesi_id' => $kayit['kontrol_maddesi_id'],
                'tarih' => $tarih,
                'girilen_deger' => $kayit['girilen_deger'] ?? null,
                'baslangic_saati' => $kayit['baslangic_saati'] ?? null,
                'bitis_saati' => $kayit['bitis_saati'] ?? null,
                'yapan_kullanici_id' => $validated['personel_id'],
                'durum' => $kayit['durum'],
                'aciklama' => $validated['genel_aciklama'] ?? null,
                'onay_durumu' => 'bekliyor',
                'ip_adresi' => $ipAddress,
                'dosyalar' => $genelDosyalar,
                'fotograflar' => $kontrolFotograflar,
            ]);
        }
    }
    
    private function kontrolGerekliMi(KontrolMaddesi $madde, Bina $bina, Carbon $tarih): bool
    {
        if ($this->hasRecordForToday($madde, $bina, $tarih)) {
            return false;
        }
        
        return $this->checkPeriyot($madde, $bina, $tarih);
    }

    private function hasRecordForToday(KontrolMaddesi $madde, Bina $bina, Carbon $tarih): bool
    {
        return KontrolKaydi::where('bina_id', $bina->id)
            ->where('kontrol_maddesi_id', $madde->id)
            ->whereDate('tarih', $tarih)
            ->exists();
    }

    private function checkPeriyot(KontrolMaddesi $madde, Bina $bina, Carbon $tarih): bool
    {
        return match($madde->periyot) {
            self::PERIYOT_GUNLUK => true,
            self::PERIYOT_HAFTALIK => $this->isPeriyotDue($madde, $bina, $tarih, 7),
            self::PERIYOT_15_GUN => $this->isPeriyotDue($madde, $bina, $tarih, 15),
            self::PERIYOT_AYLIK => $this->isPeriyotDueInMonths($madde, $bina, $tarih, 1),
            default => false,
        };
    }

    private function isPeriyotDue(KontrolMaddesi $madde, Bina $bina, Carbon $tarih, int $days): bool
    {
        $sonKayit = $this->getLastRecord($madde, $bina);
        return !$sonKayit || $sonKayit->tarih->diffInDays($tarih) >= $days;
    }

    private function isPeriyotDueInMonths(KontrolMaddesi $madde, Bina $bina, Carbon $tarih, int $months): bool
    {
        $sonKayit = $this->getLastRecord($madde, $bina);
        return !$sonKayit || $sonKayit->tarih->diffInMonths($tarih) >= $months;
    }

    private function getLastRecord(KontrolMaddesi $madde, Bina $bina): ?KontrolKaydi
    {
        return KontrolKaydi::where('bina_id', $bina->id)
            ->where('kontrol_maddesi_id', $madde->id)
            ->latest('tarih')
            ->first();
    }
}
