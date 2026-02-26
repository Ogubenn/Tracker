<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArsivlenmisIs extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'arsivlenmis_isler';

    protected $fillable = [
        'bina_id',
        'is_tarihi',
        'is_aciklamasi',
        'detayli_aciklama',
        'fotograflar',
        'olusturan_kullanici_id',
    ];

    protected $casts = [
        'is_tarihi' => 'date',
        'fotograflar' => 'array',
    ];

    /**
     * İşi oluşturan kullanıcı
     */
    public function olusturan()
    {
        return $this->belongsTo(User::class, 'olusturan_kullanici_id');
    }

    /**
     * İşin yapıldığı bina
     */
    public function bina()
    {
        return $this->belongsTo(Bina::class);
    }

    /**
     * Fotoğraf URL'lerini al
     */
    public function getFotografUrlsAttribute()
    {
        if (!$this->fotograflar) {
            return [];
        }

        return array_map(function ($path) {
            return asset('storage/' . $path);
        }, $this->fotograflar);
    }
}
