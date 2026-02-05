<?php

namespace Tests\Feature;

use App\Models\Medicine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_medicines_list()
    {
        Medicine::create([
            'kode_obat' => 'MED001',
            'nama_obat' => 'Paracetamol',
            'jenis_obat' => 'Tablet',
            'satuan_obat' => 'Strip',
            'stok_obat' => 100,
            'harga_obat' => 5000,
        ]);

        $response = $this->getJson('/api/medicines');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => ['id', 'nama_obat', 'stok_obat', 'harga_obat'],
                ],
            ]);
    }

    public function test_can_create_order()
    {
        $medicine = Medicine::create([
            'kode_obat' => 'MED002',
            'nama_obat' => 'Amoxicillin',
            'jenis_obat' => 'Tablet',
            'satuan_obat' => 'Strip',
            'stok_obat' => 50,
            'harga_obat' => 10000,
        ]);

        $payload = [
            'transaction_date' => now()->toDateString(),
            'items' => [
                [
                    'medicine_id' => $medicine->id,
                    'quantity' => 5,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('transactions', [
            'total_amount' => 50000,
        ]);

        $this->assertDatabaseHas('transaction_items', [
            'medicine_id' => $medicine->id,
            'quantity' => 5,
        ]);

        // Check stock reduced
        $this->assertDatabaseHas('medicines', [
            'id' => $medicine->id,
            'stok_obat' => 45,
        ]);
    }

    public function test_cannot_order_more_than_stock()
    {
        $medicine = Medicine::create([
            'kode_obat' => 'MED003',
            'nama_obat' => 'Limited Med',
            'jenis_obat' => 'Tablet',
            'satuan_obat' => 'Strip',
            'stok_obat' => 5,
            'harga_obat' => 10000,
        ]);

        $payload = [
            'transaction_date' => now()->toDateString(),
            'items' => [
                [
                    'medicine_id' => $medicine->id,
                    'quantity' => 10,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(400)
            ->assertJson(['status' => 'error']);

        // Stock remains same
        $this->assertDatabaseHas('medicines', [
            'id' => $medicine->id,
            'stok_obat' => 5,
        ]);
    }
}
