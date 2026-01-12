<?php

namespace Database\Factories;

use App\Models\KontrolMaddesi;
use App\Models\Alan;
use Illuminate\Database\Eloquent\Factories\Factory;

class KontrolMaddesiFactory extends Factory
{
    protected $model = KontrolMaddesi::class;

    public function definition(): array
    {
        $periyot = fake()->randomElement(['gunluk', 'haftalik', '15_gun', 'aylik']);
        
        return [
            'alan_id' => Alan::factory(),
            'kontrol_adi' => fake()->randomElement([
                'Pompa çalışıyor mu?',
                'Yağ seviyesi normal mi?',
                'Sıcaklık kontrolü',
                'Debi ölçümü',
                'pH seviyesi',
                'Koku kontrolü',
                'Görsel kontrol',
                'Ses kontrolü',
            ]),
            'kontrol_tipi' => fake()->randomElement(['checkbox', 'sayisal', 'metin']),
            'periyot' => $periyot,
            'haftalik_gun' => $periyot === 'haftalik' 
                ? fake()->randomElement(['pazartesi', 'sali', 'carsamba', 'persembe', 'cuma', 'cumartesi', 'pazar'])
                : null,
            'aktif_mi' => true,
            'sira' => fake()->numberBetween(1, 100),
        ];
    }
}
