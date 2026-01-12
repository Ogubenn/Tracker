<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bina;
use App\Models\KontrolMaddesi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin kullanıcı oluştur
        User::create([
            'ad' => 'Admin',
            'email' => 'admin@atiksu.com',
            'password' => Hash::make('password'),
            'rol' => 'admin',
            'aktif_mi' => true,
        ]);

        // Personel kullanıcıları oluştur
        User::create([
            'ad' => 'Ahmet Yılmaz',
            'email' => 'personel@atiksu.com',
            'password' => Hash::make('password'),
            'rol' => 'personel',
            'aktif_mi' => true,
        ]);

        User::create([
            'ad' => 'Mehmet Demir',
            'email' => 'mehmet@atiksu.com',
            'password' => Hash::make('password'),
            'rol' => 'personel',
            'aktif_mi' => true,
        ]);

        // Binalar oluştur
        $binalar = [
            'Ön Arıtma Binası',
            'Birincil Arıtma Binası',
            'İkincil Arıtma Binası',
            'Çamur Yönetim Binası',
            'Kimyasal Depolama',
            'Jeneratör Binası',
            'Kontrol Merkezi',
        ];

        foreach ($binalar as $binaAdi) {
            $bina = Bina::create([
                'bina_adi' => $binaAdi,
                'aktif_mi' => true,
            ]);

            // Her binaya kontrol maddeleri ekle
            $this->createKontrolMaddeleri($bina);
        }
    }

    private function createKontrolMaddeleri(Bina $bina)
    {
        // Günlük kontroller
        KontrolMaddesi::create([
            'bina_id' => $bina->id,
            'kontrol_adi' => 'Genel görsel kontrol',
            'kontrol_tipi' => 'checkbox',
            'periyot' => 'gunluk',
            'aktif_mi' => true,
            'sira' => 1,
        ]);

        KontrolMaddesi::create([
            'bina_id' => $bina->id,
            'kontrol_adi' => 'Koku kontrolü',
            'kontrol_tipi' => 'checkbox',
            'periyot' => 'gunluk',
            'aktif_mi' => true,
            'sira' => 2,
        ]);

        KontrolMaddesi::create([
            'bina_id' => $bina->id,
            'kontrol_adi' => 'Sıcaklık ölçümü (°C)',
            'kontrol_tipi' => 'sayisal',
            'periyot' => 'gunluk',
            'aktif_mi' => true,
            'sira' => 3,
        ]);

        // Haftalık kontrol (Pazartesi)
        KontrolMaddesi::create([
            'bina_id' => $bina->id,
            'kontrol_adi' => 'Haftalık detaylı temizlik kontrolü',
            'kontrol_tipi' => 'checkbox',
            'periyot' => 'haftalik',
            'haftalik_gun' => 'pazartesi',
            'aktif_mi' => true,
            'sira' => 4,
        ]);

        // 15 günlük kontrol
        KontrolMaddesi::create([
            'bina_id' => $bina->id,
            'kontrol_adi' => 'Ekipman bakım kontrolü',
            'kontrol_tipi' => 'metin',
            'periyot' => '15_gun',
            'aktif_mi' => true,
            'sira' => 5,
        ]);

        // Aylık kontrol
        KontrolMaddesi::create([
            'bina_id' => $bina->id,
            'kontrol_adi' => 'Aylık kapsamlı kontrol',
            'kontrol_tipi' => 'metin',
            'periyot' => 'aylik',
            'aktif_mi' => true,
            'sira' => 6,
        ]);
    }
}
