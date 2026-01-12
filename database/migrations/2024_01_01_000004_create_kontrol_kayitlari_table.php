<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kontrol_kayitlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrol_maddesi_id')->constrained('kontrol_maddeleri')->onDelete('cascade');
            $table->date('tarih');
            $table->string('girilen_deger')->nullable()->comment('Checkbox için: 1/0, Sayısal için: değer, Metin için: metin');
            $table->foreignId('yapan_kullanici_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Bir kontrol maddesi için aynı gün birden fazla kayıt olmasın
            $table->unique(['kontrol_maddesi_id', 'tarih'], 'unique_kontrol_tarih');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrol_kayitlari');
    }
};
