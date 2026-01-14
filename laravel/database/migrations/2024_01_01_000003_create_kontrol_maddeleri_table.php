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
        Schema::create('kontrol_maddeleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alan_id')->constrained('alanlar')->onDelete('cascade');
            $table->string('kontrol_adi');
            $table->enum('kontrol_tipi', ['checkbox', 'sayisal', 'metin'])->default('checkbox');
            $table->enum('periyot', ['gunluk', 'haftalik', '15_gun', 'aylik'])->default('gunluk');
            $table->enum('haftalik_gun', ['pazartesi', 'sali', 'carsamba', 'persembe', 'cuma', 'cumartesi', 'pazar'])->nullable()->comment('Sadece haftalık kontroller için');
            $table->boolean('aktif_mi')->default(true);
            $table->integer('sira')->default(0)->comment('Gösterim sırası');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrol_maddeleri');
    }
};
