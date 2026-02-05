<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['transaction_date', 'total_amount'];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
