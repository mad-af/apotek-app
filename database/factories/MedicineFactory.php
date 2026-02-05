<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medicine>
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_obat' => 'MED-' . $this->faker->unique()->numberBetween(1000, 9999),
            'nama_obat' => $this->faker->words(2, true) . ' ' . $this->faker->randomElement(['500mg', '100ml', '10mg', 'Syrup']),
            'satuan_obat' => $this->faker->randomElement(['Tablet', 'Botol', 'Strip', 'Kapsul', 'Tube']),
            'harga_obat' => $this->faker->numberBetween(5000, 200000),
            'stok_obat' => $this->faker->numberBetween(10, 1000),
        ];
    }
}
