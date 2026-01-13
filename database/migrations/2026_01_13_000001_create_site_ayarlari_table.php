<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_ayarlari', function (Blueprint $table) {
            $table->id();
            $table->string('anahtar')->unique();
            $table->text('deger')->nullable();
            $table->timestamps();
        });

        DB::table('site_ayarlari')->insert([
            ['anahtar' => 'eksik_kontrol_mail_aktif', 'deger' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['anahtar' => 'eksik_kontrol_sabah_saat', 'deger' => '07:00', 'created_at' => now(), 'updated_at' => now()],
            ['anahtar' => 'eksik_kontrol_aksam_saat', 'deger' => '19:00', 'created_at' => now(), 'updated_at' => now()],
            ['anahtar' => 'toplu_rapor_mail_aktif', 'deger' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['anahtar' => 'toplu_rapor_saat', 'deger' => '19:00', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_ayarlari');
    }
};
