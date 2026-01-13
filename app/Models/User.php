<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'ad',
        'email',
        'password',
        'rol',
        'aktif_mi',
        'qr_gorunur',
        'mail_alsin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'aktif_mi' => 'boolean',
            'qr_gorunur' => 'boolean',
            'mail_alsin' => 'boolean',
        ];
    }

    public function getNameAttribute(): string
    {
        return $this->ad;
    }

    public function kontrolKayitlari(): HasMany
    {
        return $this->hasMany(KontrolKaydi::class, 'yapan_kullanici_id');
    }

    public function isAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function isPersonel(): bool
    {
        return $this->rol === 'personel';
    }

    public function isActive(): bool
    {
        return $this->aktif_mi;
    }
}
