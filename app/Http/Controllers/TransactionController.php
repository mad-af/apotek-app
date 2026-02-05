<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function print(Transaction $transaction)
    {
        $transaction->load('items.medicine');
        return view('transactions.print', compact('transaction'));
    }
}
