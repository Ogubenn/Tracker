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
        Schema::create('alanlar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bina_id')->constrained('binalar')->onDelete('cascade');
            $table->string('alan_adi');
            $table->boolean('aktif_mi')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alanlar');
    }
};
