<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laboratuvar_parametreler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapor_id')->constrained('laboratuvar_raporlar')->onDelete('cascade');
            $table->string('parametre_adi'); // Biyokimyasal Oksijen İhtiyacı, pH, Sıcaklık
            $table->string('birim')->nullable(); // mg/L, °C, -
            $table->decimal('analiz_sonucu', 12, 4)->nullable(); // 4.05, 7.43
            $table->string('limit_degeri')->nullable(); // 25, -, 15 mg/L N (10000-100000 E.N.)
            $table->string('analiz_metodu')->nullable(); // SM 5210 B, SM 4500 H+ B
            $table->enum('uygunluk', ['uygun', 'uygun_degil', 'limit_yok'])->nullable(); // Otomatik hesaplanacak
            $table->integer('tablo_no')->nullable(); // 1, 2 (EK-IV'te hangi tablo)
            $table->text('notlar')->nullable();
            $table->timestamps();
            
            $table->index('rapor_id');
            $table->index('parametre_adi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laboratuvar_parametreler');
    }
};
