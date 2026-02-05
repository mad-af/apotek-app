<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // TASK 8: View Medicine Stock
    public function index()
    {
        $medicines = Medicine::select('id', 'nama_obat', 'satuan_obat', 'stok_obat', 'harga_obat')->get();

        return response()->json([
            'status' => 'success',
            'data' => $medicines,
        ]);
    }

    // TASK 7: Order Medicine
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate stock first
        foreach ($request->items as $item) {
            $medicine = Medicine::find($item['medicine_id']);
            if ($medicine->stok_obat < $item['quantity']) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Stock not sufficient for medicine: {$medicine->nama_obat}. Available: {$medicine->stok_obat}",
                ], 400);
            }
        }

        DB::beginTransaction();
        try {
            $totalAmount = 0;

            $transaction = Transaction::create([
                'transaction_date' => $request->transaction_date,
                'total_amount' => 0,
            ]);

            foreach ($request->items as $itemData) {
                $medicine = Medicine::find($itemData['medicine_id']);
                $price = $medicine->harga_obat;
                $totalPrice = $price * $itemData['quantity'];

                // Using create() triggers the 'created' event in TransactionItem model
                // which handles stock decrement automatically.
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'medicine_id' => $medicine->id,
                    'quantity' => $itemData['quantity'],
                    'price' => $price,
                    'total_price' => $totalPrice,
                ]);

                $totalAmount += $totalPrice;
            }

            $transaction->update(['total_amount' => $totalAmount]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'data' => $transaction->load('items'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
