<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personel_devam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tarih');
            $table->boolean('giris_yapti')->default(false);
            $table->boolean('cikis_yapti')->default(false);
            $table->enum('durum', ['calisma', 'izinli', 'raporlu', 'gelmedi'])->default('calisma');
            $table->text('notlar')->nullable();
            $table->foreignId('kaydeden_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Aynı personel aynı gün için tek kayıt
            $table->unique(['user_id', 'tarih']);
            
            $table->index('tarih');
            $table->index(['user_id', 'tarih']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personel_devam');
    }
};
