<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteAyarlari extends Model
{
    protected $table = 'site_ayarlari';

    protected $fillable = ['anahtar', 'deger'];

    public static function get(string $anahtar, mixed $default = null): mixed
    {
        return Cache::remember("ayar_{$anahtar}", 3600, function () use ($anahtar, $default) {
            $ayar = self::where('anahtar', $anahtar)->first();
            return $ayar ? $ayar->deger : $default;
        });
    }

    public static function set(string $anahtar, mixed $deger): void
    {
        self::updateOrCreate(
            ['anahtar' => $anahtar],
            ['deger' => $deger]
        );

        Cache::forget("ayar_{$anahtar}");
    }

    public static function getBool(string $anahtar): bool
    {
        return (bool) self::get($anahtar, false);
    }
}
