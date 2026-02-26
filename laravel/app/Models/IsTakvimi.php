<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class IsTakvimi extends Model
{
    use HasFactory;

    protected $table = 'is_takvimi';

    protected $fillable = [
        'baslik',
        'tarih',
        'atanan_kullanici_id',
        'durum',
        'renk_kategori',
        'tekrarli_mi',
        'tekrar_gun',
    ];

    protected $casts = [
        'tarih' => 'date',
        'tekrarli_mi' => 'boolean',
        'tekrar_gun' => 'integer',
    ];

    /**
     * İlişki: Atanan kullanıcı
     */
    public function atananKullanici()
    {
        return $this->belongsTo(User::class, 'atanan_kullanici_id');
    }
    
    /**
     * İlişki: Atanan kullanıcılar (çoklu)
     */
    public function atananKullanicilar()
    {
        return $this->belongsToMany(User::class, 'is_takvimi_kullanici', 'is_takvimi_id', 'user_id')->withTimestamps();
    }

    /**
     * Scope: Bugünkü işler
     */
    public function scopeBugun($query)
    {
        return $query->whereDate('tarih', Carbon::today());
    }

    /**
     * Scope: Tamamlanmış işler
     */
    public function scopeTamamlanan($query)
    {
        return $query->where('durum', 'tamamlandi');
    }

    /**
     * Scope: Gecikmiş işler
     */
    public function scopeGeciken($query)
    {
        return $query->where('durum', 'gecikti')
                    ->orWhere(function($q) {
                        $q->where('durum', 'bekliyor')
                          ->whereDate('tarih', '<', Carbon::today());
                    });
    }

    /**
     * Scope: Belirli ay ve yıl
     */
    public function scopeAylik($query, $yil, $ay)
    {
        return $query->whereYear('tarih', $yil)
                    ->whereMonth('tarih', $ay);
    }

    /**
     * Takvim rengi döndür
     */
    public function getRenkAttribute()
    {
        if ($this->durum === 'tamamlandi') {
            return '#10B981'; // Yeşil
        }
        
        if ($this->tarih->isToday()) {
            return '#F59E0B'; // Sarı (bugün)
        }
        
        if ($this->durum === 'gecikti' || ($this->durum === 'bekliyor' && $this->tarih->isPast())) {
            return '#EF4444'; // Kırmızı (gecikmiş)
        }
        
        if ($this->renk_kategori === 'gece') {
            return '#3B82F6'; // Mavi (gece çalışanları)
        }
        
        return '#6B7280'; // Gri (normal bekleyen)
    }

    /**
     * Durumu otomatik güncelle
     */
    public static function gecikenleriGuncelle()
    {
        self::where('durum', 'bekliyor')
            ->whereDate('tarih', '<', Carbon::today())
            ->update(['durum' => 'gecikti']);
    }
}
