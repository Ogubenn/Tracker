<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaboratuvarParametre extends Model
{
    use HasFactory;

    protected $table = 'laboratuvar_parametreler';

    protected $fillable = [
        'rapor_id',
        'parametre_adi',
        'birim',
        'analiz_sonucu',
        'limit_degeri',
        'analiz_metodu',
        'uygunluk',
        'tablo_no',
        'notlar',
    ];

    protected $casts = [
        'analiz_sonucu' => 'decimal:4',
    ];

    public function rapor(): BelongsTo
    {
        return $this->belongsTo(LaboratuvarRapor::class, 'rapor_id');
    }

    // Uygunluk durumunu hesapla
    public function hesaplaUygunluk(): ?string
    {
        if (empty($this->limit_degeri) || $this->limit_degeri == '-') {
            return 'limit_yok';
        }

        // Basit limit karşılaştırması
        // Örnek: "25" -> limit 25
        // Örnek: "15 mg/L N (10000-100000 E.N.)" -> karmaşık
        
        $limitStr = $this->limit_degeri;
        
        // Sadece sayı varsa
        if (is_numeric($limitStr)) {
            $limit = (float) $limitStr;
            return $this->analiz_sonucu <= $limit ? 'uygun' : 'uygun_degil';
        }
        
        // Aralık varsa (10000-100000 gibi)
        if (preg_match('/(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)/', $limitStr, $matches)) {
            $minLimit = (float) $matches[1];
            $maxLimit = (float) $matches[2];
            $deger = $this->analiz_sonucu;
            
            return ($deger >= $minLimit && $deger <= $maxLimit) ? 'uygun' : 'uygun_degil';
        }
        
        // İlk sayıyı al
        if (preg_match('/(\d+(?:\.\d+)?)/', $limitStr, $matches)) {
            $limit = (float) $matches[1];
            return $this->analiz_sonucu <= $limit ? 'uygun' : 'uygun_degil';
        }
        
        return 'limit_yok';
    }

    // Uygunluk badge rengi
    public function getUygunlukBadgeClass(): string
    {
        return match($this->uygunluk) {
            'uygun' => 'bg-success',
            'uygun_degil' => 'bg-danger',
            'limit_yok' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    // Uygunluk metni
    public function getUygunlukText(): string
    {
        return match($this->uygunluk) {
            'uygun' => 'Uygun',
            'uygun_degil' => 'Uygun Değil',
            'limit_yok' => 'Limit Yok',
            default => '-',
        };
    }

    // Scope: Parametreye göre
    public function scopeParametre($query, $parametreAdi)
    {
        return $query->where('parametre_adi', 'like', '%' . $parametreAdi . '%');
    }

    // Scope: Uygun olmayanlar
    public function scopeUygunOlmayanlar($query)
    {
        return $query->where('uygunluk', 'uygun_degil');
    }
}
