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
        Schema::table('kontrol_kayitlari', function (Blueprint $table) {
            $table->foreignId('bina_id')->after('id')->constrained('binalar')->onDelete('cascade');
            $table->text('aciklama')->nullable()->after('girilen_deger');
            $table->enum('durum', ['uygun', 'uygun_degil', 'duzeltme_gerekli'])->default('uygun')->after('aciklama');
            $table->enum('onay_durumu', ['bekliyor', 'onaylandi', 'reddedildi'])->default('bekliyor')->after('durum');
            $table->text('admin_notu')->nullable()->after('onay_durumu');
            $table->foreignId('onaylayan_id')->nullable()->after('admin_notu')->constrained('users')->onDelete('set null');
            $table->timestamp('onay_tarihi')->nullable()->after('onaylayan_id');
            $table->string('ip_adresi', 45)->nullable()->after('onay_tarihi');
            $table->json('dosyalar')->nullable()->after('ip_adresi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontrol_kayitlari', function (Blueprint $table) {
            $table->dropForeign(['bina_id']);
            $table->dropForeign(['onaylayan_id']);
            $table->dropColumn(['bina_id', 'aciklama', 'durum', 'onay_durumu', 'admin_notu', 'onaylayan_id', 'onay_tarihi', 'ip_adresi', 'dosyalar']);
        });
    }
};
