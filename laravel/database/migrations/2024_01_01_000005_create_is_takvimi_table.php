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
        Schema::create('is_takvimi', function (Blueprint $table) {
            $table->id();
            $table->string('baslik');
            $table->text('aciklama')->nullable();
            $table->date('tarih');
            $table->foreignId('atanan_kullanici_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('durum', ['bekliyor', 'tamamlandi', 'gecikti'])->default('bekliyor');
            $table->enum('renk_kategori', ['normal', 'gece'])->default('normal');
            $table->boolean('tekrarli_mi')->default(false);
            $table->integer('tekrar_gun')->nullable()->comment('Ayın kaçıncı günü tekrarlanacak (1-31)');
            $table->timestamps();
            
            $table->index('tarih');
            $table->index('atanan_kullanici_id');
            $table->index('durum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('is_takvimi');
    }
};
