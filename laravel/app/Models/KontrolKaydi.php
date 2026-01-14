<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KontrolKaydi extends Model
{
    use HasFactory;

    protected $table = 'kontrol_kayitlari';

    protected $fillable = [
        'bina_id',
        'kontrol_maddesi_id',
        'tarih',
        'girilen_deger',
        'baslangic_saati',
        'bitis_saati',
        'yapan_kullanici_id',
        'aciklama',
        'durum',
        'onay_durumu',
        'admin_notu',
        'onaylayan_id',
        'onay_tarihi',
        'ip_adresi',
        'dosyalar',
        'fotograflar',
    ];

    protected $casts = [
        'tarih' => 'date',
        'onay_tarihi' => 'datetime',
        'dosyalar' => 'array',
        'fotograflar' => 'array',
    ];

    public function bina(): BelongsTo
    {
        return $this->belongsTo(Bina::class, 'bina_id');
    }

    public function kontrolMaddesi(): BelongsTo
    {
        return $this->belongsTo(KontrolMaddesi::class, 'kontrol_maddesi_id');
    }

    public function yapanKullanici(): BelongsTo
    {
        return $this->belongsTo(User::class, 'yapan_kullanici_id');
    }

    public function onaylayan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'onaylayan_id');
    }

    public function scopeBugun($query)
    {
        return $query->whereDate('tarih', Carbon::today());
    }

    public function scopeTarihAralik($query, Carbon $baslangic, Carbon $bitis)
    {
        return $query->whereBetween('tarih', [$baslangic, $bitis]);
    }

    public function scopeBina($query, int $binaId)
    {
        return $query->where('bina_id', $binaId);
    }

    public function scopeBekleyen($query)
    {
        return $query->where('onay_durumu', 'bekliyor');
    }

    public function scopeOnaylanan($query)
    {
        return $query->where('onay_durumu', 'onaylandi');
    }

    public function scopeKullanici($query, int $kullaniciId)
    {
        return $query->where('yapan_kullanici_id', $kullaniciId);
    }

    /**
     * Fotoğraf var mı kontrol et
     */
    public function hasFotograflar(): bool
    {
        return !empty($this->fotograflar) && is_array($this->fotograflar);
    }

    /**
     * Fotoğraf sayısını getir
     */
    public function fotografSayisi(): int
    {
        return $this->hasFotograflar() ? count($this->fotograflar) : 0;
    }

    /**
     * Fotoğraf URL'lerini getir
     */
    public function getFotografUrls(): array
    {
        if (!$this->hasFotograflar()) {
            return [];
        }

        return array_map(function ($path) {
            return \Storage::disk('public')->url($path);
        }, $this->fotograflar);
    }
}
