<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kontrol_maddeleri', function (Blueprint $table) {
            $table->dropForeign(['alan_id']);
            $table->renameColumn('alan_id', 'bina_id');
            $table->foreign('bina_id')->references('id')->on('binalar')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('kontrol_maddeleri', function (Blueprint $table) {
            $table->dropForeign(['bina_id']);
            $table->renameColumn('bina_id', 'alan_id');
            $table->foreign('alan_id')->references('id')->on('alanlar')->onDelete('cascade');
        });
    }
};
