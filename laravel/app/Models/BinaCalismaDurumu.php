<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class BinaCalismaDurumu extends Model
{
    use HasFactory;

    protected $table = 'bina_calisma_durumlari';

    protected $fillable = [
        'bina_id',
        'tarih',
        'durum',
        'kullanici_id',
        'aciklama',
    ];

    protected $casts = [
        'tarih' => 'date',
    ];

    public function bina(): BelongsTo
    {
        return $this->belongsTo(Bina::class, 'bina_id');
    }

    public function kullanici(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kullanici_id');
    }

    /**
     * Belirli bir bina ve tarih için çalışmadı kaydı var mı?
     */
    public static function binaCalismiyor(int $binaId, Carbon|string $tarih): bool
    {
        return self::where('bina_id', $binaId)
            ->where('tarih', Carbon::parse($tarih)->format('Y-m-d'))
            ->where('durum', 'calismadi')
            ->exists();
    }

    /**
     * Belirli bir tarih için çalışmayan binaların ID'lerini getir
     */
    public static function calismayanBinalar(Carbon|string $tarih): array
    {
        return self::where('tarih', Carbon::parse($tarih)->format('Y-m-d'))
            ->where('durum', 'calismadi')
            ->pluck('bina_id')
            ->toArray();
    }
}
