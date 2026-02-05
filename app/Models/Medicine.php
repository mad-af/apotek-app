<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'satuan_obat',
        'harga_obat',
        'stok_obat',
    ];

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
