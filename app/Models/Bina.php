<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bina extends Model
{
    use HasFactory;

    protected $table = 'binalar';

    protected $fillable = [
        'bina_adi',
        'uuid',
        'aktif_mi',
    ];

    protected $casts = [
        'aktif_mi' => 'boolean',
    ];

    /**
     * Boot method - UUID otomatik oluştur
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bina) {
            if (empty($bina->uuid)) {
                $bina->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Binaya ait kontrol maddeleri
     */
    public function kontrolMaddeleri()
    {
        return $this->hasMany(KontrolMaddesi::class, 'bina_id');
    }

    /**
     * Binaya ait kontrol kayıtları
     */
    public function kontrolKayitlari()
    {
        return $this->hasMany(KontrolKaydi::class, 'bina_id');
    }

    /**
     * Binaya ait aktif kontrol maddeleri
     */
    public function aktifKontrolMaddeleri()
    {
        return $this->hasMany(KontrolMaddesi::class, 'bina_id')->where('aktif_mi', true);
    }

    /**
     * Scope: Sadece aktif binaları getir
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif_mi', true);
    }
}
