<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KontrolMaddesi extends Model
{
    use HasFactory;

    protected $table = 'kontrol_maddeleri';

    protected $fillable = [
        'bina_id',
        'kontrol_adi',
        'kontrol_tipi',
        'birim',
        'zaman_secimi',
        'periyot',
        'haftalik_gun',
        'aktif_mi',
        'sira',
    ];

    protected $casts = [
        'aktif_mi' => 'boolean',
        'zaman_secimi' => 'boolean',
        'sira' => 'integer',
    ];

    private const PERIYOT_GUNLUK = 'gunluk';
    private const PERIYOT_HAFTALIK = 'haftalik';
    private const PERIYOT_15_GUN = '15_gun';
    private const PERIYOT_AYLIK = 'aylik';

    private const GUN_MAP = [
        'monday' => 'pazartesi',
        'tuesday' => 'sali',
        'wednesday' => 'carsamba',
        'thursday' => 'persembe',
        'friday' => 'cuma',
        'saturday' => 'cumartesi',
        'sunday' => 'pazar',
    ];

    public function alan(): BelongsTo
    {
        return $this->belongsTo(Alan::class, 'alan_id');
    }

    public function bina(): BelongsTo
    {
        return $this->belongsTo(Bina::class, 'bina_id');
    }

    public function kontrolKayitlari(): HasMany
    {
        return $this->hasMany(KontrolKaydi::class, 'kontrol_maddesi_id');
    }

    public function bugunKaydiVarMi(): bool
    {
        return $this->tarihteKaydiVarMi(Carbon::today());
    }

    public function tarihteKaydiVarMi(Carbon $tarih): bool
    {
        return $this->kontrolKayitlari()
            ->whereDate('tarih', $tarih)
            ->exists();
    }

    public function bugunKaydi(): ?KontrolKaydi
    {
        return $this->kontrolKayitlari()
            ->whereDate('tarih', Carbon::today())
            ->first();
    }

    public function sonKayit(): ?KontrolKaydi
    {
        return $this->kontrolKayitlari()
            ->latest('tarih')
            ->first();
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif_mi', true);
    }

    public function scopePeriyot($query, string $periyot)
    {
        return $query->where('periyot', $periyot);
    }

    public function scopeSirali($query)
    {
        return $query->orderBy('sira');
    }

    public function bugunYapilmaliMi(): bool
    {
        $bugun = Carbon::today();

        return match($this->periyot) {
            self::PERIYOT_GUNLUK => true,
            self::PERIYOT_HAFTALIK => $this->isHaftalikGunToday($bugun),
            self::PERIYOT_15_GUN => $this->is15GunGecti($bugun),
            self::PERIYOT_AYLIK => $this->buAyYapildiMi($bugun),
            default => false,
        };
    }

    private function isHaftalikGunToday(Carbon $bugun): bool
    {
        $gunAdi = strtolower($bugun->translatedFormat('l'));
        $turkceGun = self::GUN_MAP[$gunAdi] ?? null;
        
        return $turkceGun === $this->haftalik_gun;
    }

    private function is15GunGecti(Carbon $bugun): bool
    {
        $sonKayit = $this->sonKayit();
        
        if (!$sonKayit) {
            return true;
        }
        
        return $sonKayit->tarih->diffInDays($bugun) >= 15;
    }

    private function buAyYapildiMi(Carbon $bugun): bool
    {
        $buAyKayit = $this->kontrolKayitlari()
            ->whereYear('tarih', $bugun->year)
            ->whereMonth('tarih', $bugun->month)
            ->exists();
            
        return !$buAyKayit;
    }
}
