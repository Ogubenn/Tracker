<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ad',
        'email',
        'password',
        'rol',
        'aktif_mi',
        'qr_gorunur',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'aktif_mi' => 'boolean',
            'qr_gorunur' => 'boolean',
        ];
    }

    /**
     * Name attribute accessor (ad sütunundan döner)
     */
    public function getNameAttribute()
    {
        return $this->ad;
    }

    /**
     * Kullanıcının yaptığı kontrol kayıtları
     */
    public function kontrolKayitlari()
    {
        return $this->hasMany(KontrolKaydi::class, 'yapan_kullanici_id');
    }

    /**
     * Admin mi kontrolü
     */
    public function isAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    /**
     * Personel mi kontrolü
     */
    public function isPersonel(): bool
    {
        return $this->rol === 'personel';
    }

    /**
     * Aktif mi kontrolü
     */
    public function isActive(): bool
    {
        return $this->aktif_mi;
    }
}
