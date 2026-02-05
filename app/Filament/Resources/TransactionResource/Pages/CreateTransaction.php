<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Medicine;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;
        $totalAmount = 0;

        foreach ($record->items as $item) {
            $medicine = Medicine::find($item->medicine_id);
            if ($medicine) {
                // Enforce system price and update item if needed
                $price = $medicine->harga_obat;
                $quantity = intval($item->quantity);
                $itemTotal = $price * $quantity;

                if ($item->price != $price || $item->total_price != $itemTotal) {
                    $item->update([
                        'price' => $price,
                        'total_price' => $itemTotal,
                    ]);
                }
                $totalAmount += $itemTotal;
            }
        }

        $record->update(['total_amount' => $totalAmount]);
    }
}
