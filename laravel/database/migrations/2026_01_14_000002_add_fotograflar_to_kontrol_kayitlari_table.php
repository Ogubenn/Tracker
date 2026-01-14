<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kontrol_kayitlari', function (Blueprint $table) {
            $table->json('fotograflar')->nullable()->after('aciklama');
        });
    }

    public function down(): void
    {
        Schema::table('kontrol_kayitlari', function (Blueprint $table) {
            $table->dropColumn('fotograflar');
        });
    }
};
