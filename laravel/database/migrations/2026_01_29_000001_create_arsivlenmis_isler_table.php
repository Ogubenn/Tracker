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
        Schema::create('arsivlenmis_isler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bina_id')->nullable()->constrained('binalar')->onDelete('set null');
            $table->date('is_tarihi');
            $table->string('is_aciklamasi');
            $table->text('detayli_aciklama')->nullable();
            $table->json('fotograflar')->nullable(); // Fotoğraf yollarını JSON olarak sakla
            $table->foreignId('olusturan_kullanici_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes(); // Silinen kayıtları geri almak için
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsivlenmis_isler');
    }
};
