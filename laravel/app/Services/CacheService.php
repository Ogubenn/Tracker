<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Cache yönetim servisi
 * 
 * Rapor ve query sonuçları için merkezi cache yönetimi sağlar.
 */
class CacheService
{
    /** @var int Cache süresi (dakika) */
    private const DEFAULT_TTL = 60;

    /** @var string Cache key prefix */
    private const PREFIX = 'atiksu_';

    /**
     * Cache'den veri al veya yoksa closure'u çalıştırıp cache'le.
     *
     * @param string $key Cache anahtarı
     * @param callable $callback Cache yoksa çalıştırılacak fonksiyon
     * @param int|null $ttl Cache süresi (dakika, null ise default)
     * @return mixed
     */
    public function remember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        $cacheKey = $this->buildKey($key);
        $ttl = $ttl ?? self::DEFAULT_TTL;

        return Cache::remember($cacheKey, $ttl * 60, $callback);
    }

    /**
     * Veriyi cache'e kaydet.
     *
     * @param string $key Cache anahtarı
     * @param mixed $value Kaydedilecek veri
     * @param int|null $ttl Cache süresi (dakika)
     * @return bool
     */
    public function put(string $key, mixed $value, ?int $ttl = null): bool
    {
        $cacheKey = $this->buildKey($key);
        $ttl = $ttl ?? self::DEFAULT_TTL;

        return Cache::put($cacheKey, $value, $ttl * 60);
    }

    /**
     * Cache'den veri al.
     *
     * @param string $key Cache anahtarı
     * @param mixed $default Varsayılan değer
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = $this->buildKey($key);
        return Cache::get($cacheKey, $default);
    }

    /**
     * Cache'den veri sil.
     *
     * @param string $key Cache anahtarı
     * @return bool
     */
    public function forget(string $key): bool
    {
        $cacheKey = $this->buildKey($key);
        return Cache::forget($cacheKey);
    }

    /**
     * Belirtilen tag'e ait tüm cache'leri sil.
     *
     * @param string|array $tags Tag(ler)
     * @return bool
     */
    public function flushTags(string|array $tags): bool
    {
        return Cache::tags($tags)->flush();
    }

    /**
     * Tüm cache'i temizle.
     *
     * @return bool
     */
    public function flush(): bool
    {
        return Cache::flush();
    }

    /**
     * Rapor cache'ini temizle.
     *
     * @return bool
     */
    public function clearReports(): bool
    {
        $keys = [
            'rapor_gunluk',
            'rapor_haftalik',
            'rapor_aylik',
            'eksik_kontroller',
            'tamamlanan_kontroller',
        ];

        foreach ($keys as $key) {
            $this->forget($key);
        }

        return true;
    }

    /**
     * Bina cache'ini temizle.
     *
     * @param int|null $binaId Belirli bina ID (null ise tümü)
     * @return bool
     */
    public function clearBina(?int $binaId = null): bool
    {
        if ($binaId) {
            return $this->forget("bina_{$binaId}");
        }

        // Tüm bina cache'lerini temizle (pattern matching)
        return $this->flushTags('binalar');
    }

    /**
     * Kontrol cache'ini temizle.
     *
     * @return bool
     */
    public function clearKontroller(): bool
    {
        return $this->flushTags('kontroller');
    }

    /**
     * Cache key'i prefix ile oluştur.
     *
     * @param string $key Orijinal key
     * @return string
     */
    private function buildKey(string $key): string
    {
        return self::PREFIX . $key;
    }

    /**
     * Cache key'i user ID ile oluştur.
     *
     * @param string $key Base key
     * @param int $userId Kullanıcı ID
     * @return string
     */
    public function buildUserKey(string $key, int $userId): string
    {
        return "{$key}_user_{$userId}";
    }

    /**
     * Cache key'i tarih ile oluştur.
     *
     * @param string $key Base key
     * @param string $date Tarih (Y-m-d formatı)
     * @return string
     */
    public function buildDateKey(string $key, string $date): string
    {
        return "{$key}_" . str_replace('-', '', $date);
    }
}
