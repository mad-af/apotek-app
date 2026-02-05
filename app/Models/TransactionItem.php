<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = ['transaction_id', 'medicine_id', 'quantity', 'price', 'total_price'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    protected static function booted()
    {
        static::created(function ($item) {
            $medicine = $item->medicine;
            if ($medicine) {
                $medicine->decrement('stok_obat', $item->quantity);
            }
        });

        static::deleted(function ($item) {
            $medicine = $item->medicine;
            if ($medicine) {
                $medicine->increment('stok_obat', $item->quantity);
            }
        });
    }
}
