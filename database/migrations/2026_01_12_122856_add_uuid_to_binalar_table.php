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
        // UUID kolonu varsa ekleme
        if (!Schema::hasColumn('binalar', 'uuid')) {
            Schema::table('binalar', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id');
            });
        }
        
        // Mevcut binalara UUID ata
        \DB::table('binalar')->whereNull('uuid')->orWhere('uuid', '')->get()->each(function ($bina) {
            \DB::table('binalar')
                ->where('id', $bina->id)
                ->update(['uuid' => \Illuminate\Support\Str::uuid()]);
        });
        
        // UUID'yi unique yap
        Schema::table('binalar', function (Blueprint $table) {
            if (!Schema::hasColumn('binalar', 'uuid') || \DB::select("SHOW INDEXES FROM binalar WHERE Key_name = 'binalar_uuid_unique'") == []) {
                $table->uuid('uuid')->unique()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('binalar', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
