<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KontrolMaddesi extends Model
{
    use HasFactory;

    protected $table = 'kontrol_maddeleri';

    protected $fillable = [
        'bina_id',
        'kontrol_adi',
        'kontrol_tipi',
        'periyot',
        'haftalik_gun',
        'aktif_mi',
        'sira',
    ];

    protected $casts = [
        'aktif_mi' => 'boolean',
        'sira' => 'integer',
    ];

    /**
     * Kontrol maddesinin bağlı olduğu bina
     */
    public function bina()
    {
        return $this->belongsTo(Bina::class, 'bina_id');
    }

    /**
     * Kontrol maddesine ait kayıtlar
     */
    public function kontrolKayitlari()
    {
        return $this->hasMany(KontrolKaydi::class, 'kontrol_maddesi_id');
    }

    /**
     * Bugün için kontrol kaydı var mı?
     */
    public function bugunKaydiVarMi(): bool
    {
        return $this->kontrolKayitlari()
            ->whereDate('tarih', Carbon::today())
            ->exists();
    }

    /**
     * Belirli bir tarih için kontrol kaydı var mı?
     */
    public function tarihteKaydiVarMi($tarih): bool
    {
        return $this->kontrolKayitlari()
            ->whereDate('tarih', $tarih)
            ->exists();
    }

    /**
     * Bugün için kontrol kaydını getir
     */
    public function bugunKaydi()
    {
        return $this->kontrolKayitlari()
            ->whereDate('tarih', Carbon::today())
            ->first();
    }

    /**
     * Son kontrol kaydını getir
     */
    public function sonKayit()
    {
        return $this->kontrolKayitlari()
            ->latest('tarih')
            ->first();
    }

    /**
     * Scope: Sadece aktif kontrol maddelerini getir
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif_mi', true);
    }

    /**
     * Scope: Periyot tipine göre filtrele
     */
    public function scopePeriyot($query, $periyot)
    {
        return $query->where('periyot', $periyot);
    }

    /**
     * Scope: Sıraya göre getir
     */
    public function scopeSirali($query)
    {
        return $query->orderBy('sira');
    }

    /**
     * Bugün bu kontrol yapılmalı mı?
     * Periyot mantığına göre karar verir
     */
    public function bugunYapilmaliMi(): bool
    {
        $bugun = Carbon::today();

        switch ($this->periyot) {
            case 'gunluk':
                // Her gün yapılmalı
                return true;

            case 'haftalik':
                // Sadece belirlenen gün yapılmalı
                $gunAdi = strtolower($bugun->translatedFormat('l'));
                $gunMap = [
                    'monday' => 'pazartesi',
                    'tuesday' => 'sali',
                    'wednesday' => 'carsamba',
                    'thursday' => 'persembe',
                    'friday' => 'cuma',
                    'saturday' => 'cumartesi',
                    'sunday' => 'pazar',
                ];
                return isset($gunMap[$gunAdi]) && $gunMap[$gunAdi] === $this->haftalik_gun;

            case '15_gun':
                // Son yapılan tarihten 15 gün geçmiş mi?
                $sonKayit = $this->sonKayit();
                if (!$sonKayit) {
                    return true; // Hiç yapılmamış, yapılmalı
                }
                return $sonKayit->tarih->diffInDays($bugun) >= 15;

            case 'aylik':
                // Bu ay içinde yapılmış mı?
                $buAyKayit = $this->kontrolKayitlari()
                    ->whereYear('tarih', $bugun->year)
                    ->whereMonth('tarih', $bugun->month)
                    ->exists();
                return !$buAyKayit; // Bu ay yapılmamışsa yapılmalı

            default:
                return false;
        }
    }
}
