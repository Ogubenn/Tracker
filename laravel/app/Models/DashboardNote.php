<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardNote extends Model
{
    protected $fillable = [
        'user_id',
        'note',
        'mail_sent_at',
    ];

    protected $casts = [
        'mail_sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
