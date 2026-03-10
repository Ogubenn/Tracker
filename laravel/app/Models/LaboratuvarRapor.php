<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaboratuvarRapor extends Model
{
    use HasFactory;

    protected $table = 'laboratuvar_raporlar';

    protected $fillable = [
        'rapor_no',
        'rapor_tarihi',
        'teklif_tarihi',
        'teklif_no',
        'tesis_adi',
        'numune_cinsi_adi',
        'numune_alma_noktasi',
        'numune_alma_noktasi_sayisi',
        'numune_alma_tarihi',
        'numune_alma_tarihi_bitis',
        'numune_alma_sekli',
        'numune_gelis_sekli',
        'numune_ambalaj',
        'numune_numarasi',
        'lab_gelis_tarihi',
        'sahit_numune',
        'analiz_baslangic',
        'analiz_bitis',
        'notlar',
        'pdf_dosya',
        'olusturan_id',
    ];

    protected $casts = [
        'rapor_tarihi' => 'date',
        'teklif_tarihi' => 'date',
        'numune_alma_tarihi' => 'datetime',
        'numune_alma_tarihi_bitis' => 'datetime',
        'lab_gelis_tarihi' => 'datetime',
        'analiz_baslangic' => 'date',
        'analiz_bitis' => 'date',
    ];

    public function parametreler(): HasMany
    {
        return $this->hasMany(LaboratuvarParametre::class, 'rapor_id');
    }

    public function olusturan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'olusturan_id');
    }

    // Scope: Tarih aralığı
    public function scopeTarihAralik($query, $baslangic, $bitis)
    {
        return $query->whereBetween('rapor_tarihi', [$baslangic, $bitis]);
    }

    // Scope: Tesis
    public function scopeTesis($query, $tesisAdi)
    {
        return $query->where('tesis_adi', 'like', '%' . $tesisAdi . '%');
    }

    // PDF varmı kontrolü
    public function hasPdf(): bool
    {
        return !empty($this->pdf_dosya) && \Storage::disk('public')->exists($this->pdf_dosya);
    }

    // PDF URL — controller üzerinden stream edilir
    public function getPdfUrl(): ?string
    {
        if (!empty($this->pdf_dosya)) {
            return route('admin.laboratuvar.pdf', $this->id);
        }
        return null;
    }
}
