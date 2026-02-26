<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // insertOrIgnore kullanarak duplicate hatasını önle
        DB::table('site_ayarlari')->insertOrIgnore([
            ['anahtar' => 'is_takvimi_hatirlatma_aktif', 'deger' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['anahtar' => 'is_takvimi_hatirlatma_saat', 'deger' => '08:00', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        DB::table('site_ayarlari')
            ->whereIn('anahtar', ['is_takvimi_hatirlatma_aktif', 'is_takvimi_hatirlatma_saat'])
            ->delete();
    }
};
