<?php

namespace App\Events;

use App\Models\KontrolKaydi;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KontrolKaydiCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Yeni kontrol kaydı event'i.
     *
     * @param KontrolKaydi $kontrolKaydi Oluşturulan kontrol kaydı
     */
    public function __construct(
        public KontrolKaydi $kontrolKaydi
    ) {}
}
