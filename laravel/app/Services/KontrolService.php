<?php

namespace App\Services;

use App\Models\Bina;
use App\Models\KontrolKaydi;
use App\Models\KontrolMaddesi;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Kontrol işlemleri için business logic servisi
 * 
 * Kontrol maddeleri, eksik kontroller ve günlük kontrol işlemlerini yönetir.
 * Mail gönderimi ve raporlama için kullanılır.
 */
class KontrolService
{
    /**
     * Bugün yapılması gereken ama yapılmamış kontrolleri döndürür
     * 
     * @return Collection<array{bina: string, kontrol: string, periyot: string}>
     */
    public function getEksikKontroller(): Collection
    {
        $eksikKontroller = collect();

        $binalar = Bina::aktif()
            ->with(['kontrolMaddeleri' => fn($q) => $q->where('aktif_mi', true)])
            ->get();

        foreach ($binalar as $bina) {
            foreach ($bina->kontrolMaddeleri as $kontrolMaddesi) {
                if ($kontrolMaddesi->bugunYapilmaliMi() && !$kontrolMaddesi->bugunKaydiVarMi()) {
                    $eksikKontroller->push([
                        'bina' => $bina->bina_adi,
                        'kontrol' => $kontrolMaddesi->kontrol_adi,
                        'periyot' => $this->periyotCevir($kontrolMaddesi->periyot),
                        'madde' => $kontrolMaddesi, // İleride detay için
                    ]);
                }
            }
        }

        return $eksikKontroller;
    }
    
    /**
     * Bugün yapılmış tüm kontrolleri döndürür
     * 
     * @return Collection<array{bina: string, kontrol: string, deger: string, user: string, zaman: string}>
     */
    public function getBugunYapilanKontroller(): Collection
    {
        $bugun = Carbon::today();
        
        return KontrolKaydi::with(['kontrolMaddesi.bina', 'user'])
            ->whereDate('tarih', $bugun)
            ->get()
            ->map(function ($kayit) {
                return [
                    'bina' => $kayit->kontrolMaddesi->bina->bina_adi,
                    'kontrol' => $kayit->kontrolMaddesi->kontrol_adi,
                    'deger' => $kayit->deger,
                    'user' => $kayit->user ? $kayit->user->ad_soyad : 'Bilinmiyor',
                    'zaman' => $kayit->tarih->format('H:i'),
                ];
            });
    }
    
    /**
     * Belirli bir bina için bugün yapılacak kontrolleri döndürür
     * 
     * @param int $binaId Bina ID
     * @return Collection<KontrolMaddesi>
     */
    public function getBugunYapilacakKontroller(int $binaId): Collection
    {
        $bina = Bina::with(['kontrolMaddeleri' => fn($q) => $q->where('aktif_mi', true)])
            ->findOrFail($binaId);
        
        return $bina->kontrolMaddeleri->filter(function ($madde) {
            return $madde->bugunYapilmaliMi();
        });
    }
    
    /**
     * Periyot kodunu Türkçe metne çevirir
     * 
     * @param string $periyot Periyot kodu (gunluk, haftalik, etc.)
     * @return string Türkçe periyot adı
     */
    public function periyotCevir(string $periyot): string
    {
        return match($periyot) {
            'gunluk' => 'Günlük',
            'haftalik' => 'Haftalık',
            '15_gun' => '15 Günlük',
            'aylik' => 'Aylık',
            default => 'Bilinmiyor',
        };
    }
    
    /**
     * Belirli tarih aralığında eksik kalan kontrolleri analiz eder
     * 
     * @param Carbon $baslangic Başlangıç tarihi
     * @param Carbon $bitis Bitiş tarihi
     * @param int|null $binaId Opsiyonel bina filtresi
     * @return Collection Eksik kontrol istatistikleri
     */
    public function getEksikKontrolAnalizi(Carbon $baslangic, Carbon $bitis, ?int $binaId = null): Collection
    {
        $query = Bina::aktif()->with('kontrolMaddeleri');
        
        if ($binaId) {
            $query->where('id', $binaId);
        }
        
        $binalar = $query->get();
        $analiz = collect();
        
        // Her gün için kontrol et
        $gunler = Carbon::parse($baslangic)->daysUntil($bitis);
        
        foreach ($binalar as $bina) {
            foreach ($bina->kontrolMaddeleri as $madde) {
                $eksikGunler = 0;
                
                foreach ($gunler as $gun) {
                    // O gün yapılmalı mıydı?
                    if ($this->kontrolGundeYapilmaliMi($madde, $gun)) {
                        // Yapıldı mı?
                        $yapildi = KontrolKaydi::where('kontrol_maddesi_id', $madde->id)
                            ->whereDate('tarih', $gun)
                            ->exists();
                        
                        if (!$yapildi) {
                            $eksikGunler++;
                        }
                    }
                }
                
                if ($eksikGunler > 0) {
                    $analiz->push([
                        'bina' => $bina->bina_adi,
                        'kontrol' => $madde->kontrol_adi,
                        'eksik_gun_sayisi' => $eksikGunler,
                        'periyot' => $this->periyotCevir($madde->periyot),
                    ]);
                }
            }
        }
        
        return $analiz;
    }
    
    /**
     * Belirli bir günde kontrolün yapılması gerekip gerekmediğini kontrol eder
     * 
     * @param KontrolMaddesi $madde
     * @param Carbon $gun
     * @return bool
     */
    private function kontrolGundeYapilmaliMi(KontrolMaddesi $madde, Carbon $gun): bool
    {
        return match($madde->periyot) {
            'gunluk' => true,
            'haftalik' => in_array(strtolower($gun->locale('tr')->dayName), $madde->haftalik_gunler ?? []),
            '15_gun' => $this->onBesGunKontrol($madde, $gun),
            'aylik' => $this->aylikKontrol($madde, $gun),
            default => false,
        };
    }
    
    /**
     * 15 günlük kontrol için gereken gün mü kontrol eder
     */
    private function onBesGunKontrol(KontrolMaddesi $madde, Carbon $gun): bool
    {
        $sonKayit = KontrolKaydi::where('kontrol_maddesi_id', $madde->id)
            ->where('tarih', '<', $gun)
            ->latest('tarih')
            ->first();
        
        if (!$sonKayit) {
            return true; // İlk kayıt, yapılmalı
        }
        
        return $sonKayit->tarih->diffInDays($gun) >= 15;
    }
    
    /**
     * Aylık kontrol için o ay yapıldı mı kontrol eder
     */
    private function aylikKontrol(KontrolMaddesi $madde, Carbon $gun): bool
    {
        $oAyYapildi = KontrolKaydi::where('kontrol_maddesi_id', $madde->id)
            ->whereYear('tarih', $gun->year)
            ->whereMonth('tarih', $gun->month)
            ->where('tarih', '<', $gun)
            ->exists();
        
        return !$oAyYapildi;
    }
}
