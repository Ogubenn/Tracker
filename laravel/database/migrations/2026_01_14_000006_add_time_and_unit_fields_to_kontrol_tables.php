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
        // Kontrol maddelerine birim ve zaman seçimi ekle
        Schema::table('kontrol_maddeleri', function (Blueprint $table) {
            $table->string('birim', 20)->nullable()->after('kontrol_tipi')->comment('Sayısal değerler için birim (m3, kg, lt, kWh vb.)');
            $table->boolean('zaman_secimi')->default(false)->after('birim')->comment('Bu kontrol için başlangıç/bitiş saati alınacak mı?');
        });

        // Kontrol kayıtlarına başlangıç ve bitiş saati ekle
        Schema::table('kontrol_kayitlari', function (Blueprint $table) {
            $table->time('baslangic_saati')->nullable()->after('girilen_deger')->comment('Kontrol başlangıç saati');
            $table->time('bitis_saati')->nullable()->after('baslangic_saati')->comment('Kontrol bitiş saati');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontrol_maddeleri', function (Blueprint $table) {
            $table->dropColumn(['birim', 'zaman_secimi']);
        });

        Schema::table('kontrol_kayitlari', function (Blueprint $table) {
            $table->dropColumn(['baslangic_saati', 'bitis_saati']);
        });
    }
};
