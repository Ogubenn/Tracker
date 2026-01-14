<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Eğer alan_id kolonu varsa (yani daha önce migration çalıştırılmamışsa)
        if (Schema::hasColumn('kontrol_maddeleri', 'alan_id')) {
            // Önce mevcut foreign key'i bul ve sil
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'kontrol_maddeleri' 
                AND COLUMN_NAME = 'alan_id' 
                AND CONSTRAINT_NAME != 'PRIMARY'
            ");
            
            foreach ($foreignKeys as $fk) {
                DB::statement("ALTER TABLE kontrol_maddeleri DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            }
            
            // Kolonu rename et
            DB::statement("ALTER TABLE kontrol_maddeleri CHANGE alan_id bina_id BIGINT UNSIGNED NOT NULL");
            
            // Yeni foreign key ekle
            DB::statement("ALTER TABLE kontrol_maddeleri ADD CONSTRAINT kontrol_maddeleri_bina_id_foreign FOREIGN KEY (bina_id) REFERENCES binalar(id) ON DELETE CASCADE");
        }
    }

    public function down(): void
    {
        // Eğer bina_id kolonu varsa (rollback için)
        if (Schema::hasColumn('kontrol_maddeleri', 'bina_id')) {
            // Önce mevcut foreign key'i bul ve sil
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'kontrol_maddeleri' 
                AND COLUMN_NAME = 'bina_id' 
                AND CONSTRAINT_NAME != 'PRIMARY'
            ");
            
            foreach ($foreignKeys as $fk) {
                DB::statement("ALTER TABLE kontrol_maddeleri DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            }
            
            // Kolonu geri rename et
            DB::statement("ALTER TABLE kontrol_maddeleri CHANGE bina_id alan_id BIGINT UNSIGNED NOT NULL");
            
            // Eski foreign key'i geri ekle (eğer alanlar tablosu varsa)
            if (Schema::hasTable('alanlar')) {
                DB::statement("ALTER TABLE kontrol_maddeleri ADD CONSTRAINT kontrol_maddeleri_alan_id_foreign FOREIGN KEY (alan_id) REFERENCES alanlar(id) ON DELETE CASCADE");
            }
        }
    }
};
