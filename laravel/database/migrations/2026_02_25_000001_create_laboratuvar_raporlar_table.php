<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laboratuvar_raporlar', function (Blueprint $table) {
            $table->id();
            $table->string('rapor_no')->unique(); // T-79051-2025-03
            $table->date('rapor_tarihi'); // 15.05.2025
            $table->string('tesis_adi'); // Bulancak Belediyesi...
            $table->string('numune_alma_noktasi')->nullable(); // Atıksu Arıtma Tesis Çıkış
            $table->dateTime('numune_alma_tarihi')->nullable(); // 27-28.01.2026 10:30/10:30
            $table->string('numune_alma_sekli')->nullable(); // 24 Saatlik Kompozit
            $table->string('numune_gelis_sekli')->nullable(); // Yerinde Alma-Korumalı-Mühürlü
            $table->string('numune_ambalaj')->nullable(); // 2 Ad. 0,5 L Plastik
            $table->string('numune_numarasi')->nullable(); // AS-29012026-005
            $table->dateTime('lab_gelis_tarihi')->nullable(); // 29.01.2026 08:00
            $table->string('sahit_numune')->nullable(); // Yok
            $table->date('analiz_baslangic')->nullable(); // 29.01.2026
            $table->date('analiz_bitis')->nullable(); // 03.02.2026
            $table->text('notlar')->nullable();
            $table->string('pdf_dosya')->nullable(); // PDF arşivi
            $table->foreignId('olusturan_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('rapor_tarihi');
            $table->index('tesis_adi');
            $table->index('analiz_baslangic');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laboratuvar_raporlar');
    }
};
