<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $bina) {
            if (empty($bina->uuid)) {
                $bina->uuid = (string) Str::uuid();
            }
        });
    }

    public function alanlar(): HasMany
    {
        return $this->hasMany(\App\Models\Alan::class, 'bina_id');
    }

    public function kontrolMaddeleri(): HasMany
    {
        return $this->hasMany(\App\Models\KontrolMaddesi::class, 'bina_id');
    }

    public function kontrolKayitlari(): HasMany
    {
        return $this->hasMany(\App\Models\KontrolKaydi::class, 'bina_id');
    }

    public function aktifKontrolMaddeleri(): HasMany
    {
        return $this->kontrolMaddeleri()->where('aktif_mi', true);
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif_mi', true);
    }
}
