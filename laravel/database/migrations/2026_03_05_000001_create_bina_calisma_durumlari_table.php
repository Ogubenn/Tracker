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
        Schema::create('bina_calisma_durumlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bina_id')->constrained('binalar')->onDelete('cascade');
            $table->date('tarih');
            $table->enum('durum', ['calismadi', 'calisma_iptal'])->default('calismadi');
            $table->foreignId('kullanici_id')->constrained('users')->onDelete('cascade');
            $table->text('aciklama')->nullable();
            $table->timestamps();
            
            // Aynı bina için aynı tarihte sadece bir kayıt olabilir
            $table->unique(['bina_id', 'tarih']);
            
            // Index'ler
            $table->index('tarih');
            $table->index(['bina_id', 'tarih']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bina_calisma_durumlari');
    }
};
