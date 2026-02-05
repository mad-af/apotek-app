<?php

namespace Database\Seeders;

use App\Models\Distributor;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User Seed
        User::factory()->create([
            'name' => 'Admin Apotek',
            'email' => 'admin@apotek.com',
            'password' => bcrypt('password'), // Ensure password is set explicitly if not default
        ]);

        // Distributor Seed
        Distributor::factory(5)->create();

        // Medicine Seed - Create some specific common medicines
        $medicines = [
            [
                'kode_obat' => 'MED-0001',
                'nama_obat' => 'Paracetamol 500mg',
                'satuan_obat' => 'Strip',
                'harga_obat' => 5000,
                'stok_obat' => 100,
            ],
            [
                'kode_obat' => 'MED-0002',
                'nama_obat' => 'Amoxicillin 500mg',
                'satuan_obat' => 'Strip',
                'harga_obat' => 12000,
                'stok_obat' => 50,
            ],
            [
                'kode_obat' => 'MED-0003',
                'nama_obat' => 'Vitamin C 1000mg',
                'satuan_obat' => 'Botol',
                'harga_obat' => 35000,
                'stok_obat' => 75,
            ],
            [
                'kode_obat' => 'MED-0004',
                'nama_obat' => 'Obat Batuk Sirup 100ml',
                'satuan_obat' => 'Botol',
                'harga_obat' => 25000,
                'stok_obat' => 40,
            ],
            [
                'kode_obat' => 'MED-0005',
                'nama_obat' => 'Masker Medis',
                'satuan_obat' => 'Box',
                'harga_obat' => 45000,
                'stok_obat' => 200,
            ],
        ];

        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }

        // Create random medicines to fill up catalog
        Medicine::factory(20)->create();
    }
}
