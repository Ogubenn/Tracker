<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log kaydı oluştur
     */
    public static function log(string $action, string $model, $modelId, ?array $oldValues = null, ?array $newValues = null, ?string $description = null): void
    {
        try {
            self::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model' => $model,
                'model_id' => $modelId,
                'description' => $description,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Activity log kaydedilemedi: ' . $e->getMessage());
        }
    }

    /**
     * İkon döndür
     */
    public function getIconAttribute(): string
    {
        return match($this->action) {
            'created' => 'bi-plus-circle text-success',
            'updated' => 'bi-pencil text-warning',
            'deleted' => 'bi-trash text-danger',
            'approved' => 'bi-check-circle text-success',
            'rejected' => 'bi-x-circle text-danger',
            default => 'bi-circle text-secondary',
        };
    }

    /**
     * Aksiyon adı döndür
     */
    public function getActionNameAttribute(): string
    {
        return match($this->action) {
            'created' => 'Oluşturuldu',
            'updated' => 'Güncellendi',
            'deleted' => 'Silindi',
            'approved' => 'Onaylandı',
            'rejected' => 'Reddedildi',
            default => $this->action,
        };
    }
}
