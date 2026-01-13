<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alan extends Model
{
    use HasFactory;

    protected $table = 'alanlar';

    protected $fillable = [
        'bina_id',
        'alan_adi',
        'aktif_mi',
    ];

    protected $casts = [
        'aktif_mi' => 'boolean',
    ];

    public function bina(): BelongsTo
    {
        return $this->belongsTo(Bina::class, 'bina_id');
    }

    public function kontrolMaddeleri(): HasMany
    {
        return $this->hasMany(KontrolMaddesi::class, 'alan_id');
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
