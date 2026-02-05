<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/transactions/{transaction}/print', [TransactionController::class, 'print'])->name('transactions.print');
