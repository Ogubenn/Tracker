<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KontrolKaydi extends Model
{
    use HasFactory;

    protected $table = 'kontrol_kayitlari';

    protected $fillable = [
        'bina_id',
        'kontrol_maddesi_id',
        'tarih',
        'girilen_deger',
        'yapan_kullanici_id',
        'aciklama',
        'durum',
        'onay_durumu',
        'admin_notu',
        'onaylayan_id',
        'onay_tarihi',
        'ip_adresi',
        'dosyalar',
    ];

    protected $casts = [
        'tarih' => 'date',
        'onay_tarihi' => 'datetime',
        'dosyalar' => 'array',
    ];

    /**
     * Kaydın bağlı olduğu bina
     */
    public function bina()
    {
        return $this->belongsTo(Bina::class, 'bina_id');
    }

    /**
     * Kaydın bağlı olduğu kontrol maddesi
     */
    public function kontrolMaddesi()
    {
        return $this->belongsTo(KontrolMaddesi::class, 'kontrol_maddesi_id');
    }

    /**
     * Kaydı yapan kullanıcı (personel)
     */
    public function yapanKullanici()
    {
        return $this->belongsTo(User::class, 'yapan_kullanici_id');
    }

    /**
     * Kaydı onaylayan admin
     */
    public function onaylayan()
    {
        return $this->belongsTo(User::class, 'onaylayan_id');
    }

    /**
     * Scope: Bugünkü kayıtlar
     */
    public function scopeBugun($query)
    {
        return $query->whereDate('tarih', Carbon::today());
    }

    /**
     * Scope: Belirli tarih aralığındaki kayıtlar
     */
    public function scopeTarihAralik($query, $baslangic, $bitis)
    {
        return $query->whereBetween('tarih', [$baslangic, $bitis]);
    }

    /**
     * Scope: Belirli bir bina için kayıtlar
     */
    public function scopeBina($query, $binaId)
    {
        return $query->where('bina_id', $binaId);
    }

    /**
     * Scope: Onay bekleyen kayıtlar
     */
    public function scopeBekleyen($query)
    {
        return $query->where('onay_durumu', 'bekliyor');
    }

    /**
     * Scope: Onaylanmış kayıtlar
     */
    public function scopeOnaylanan($query)
    {
        return $query->where('onay_durumu', 'onaylandi');
    }

    /**
     * Scope: Belirli bir kullanıcının kayıtları
     */
    public function scopeKullanici($query, $kullaniciId)
    {
        return $query->where('yapan_kullanici_id', $kullaniciId);
    }
}
