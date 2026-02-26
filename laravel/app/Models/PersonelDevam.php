<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonelDevam extends Model
{
    use HasFactory;

    protected $table = 'personel_devam';

    protected $fillable = [
        'user_id',
        'tarih',
        'giris_yapti',
        'cikis_yapti',
        'durum',
        'notlar',
        'kaydeden_id',
    ];

    protected $casts = [
        'tarih' => 'date',
        'giris_yapti' => 'boolean',
        'cikis_yapti' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kaydeden(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kaydeden_id');
    }

    // Duruma göre badge rengi
    public function getDurumBadgeClass(): string
    {
        return match($this->durum) {
            'calisma' => 'bg-success',
            'izinli' => 'bg-info',
            'raporlu' => 'bg-warning',
            'gelmedi' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    // Duruma göre Türkçe metin
    public function getDurumText(): string
    {
        return match($this->durum) {
            'calisma' => 'Çalıştı',
            'izinli' => 'İzinli',
            'raporlu' => 'Raporlu',
            'gelmedi' => 'Gelmedi',
            default => 'Bilinmiyor',
        };
    }

    // Scope: Belirli tarih aralığı
    public function scopeTarihAralik($query, $baslangic, $bitis)
    {
        return $query->whereBetween('tarih', [$baslangic, $bitis]);
    }

    // Scope: Sadece çalışanlar
    public function scopeCalisanlar($query)
    {
        return $query->where('durum', 'calisma');
    }
}
