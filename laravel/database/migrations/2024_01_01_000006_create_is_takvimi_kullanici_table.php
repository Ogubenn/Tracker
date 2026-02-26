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
        Schema::create('is_takvimi_kullanici', function (Blueprint $table) {
            $table->id();
            $table->foreignId('is_takvimi_id')->constrained('is_takvimi')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['is_takvimi_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('is_takvimi_kullanici');
    }
};
