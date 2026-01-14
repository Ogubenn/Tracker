<?php

namespace Database\Factories;

use App\Models\Bina;
use Illuminate\Database\Eloquent\Factories\Factory;

class BinaFactory extends Factory
{
    protected $model = Bina::class;

    public function definition(): array
    {
        return [
            'bina_adi' => 'Bina ' . fake()->unique()->numberBetween(1, 100),
            'aktif_mi' => true,
        ];
    }
}
