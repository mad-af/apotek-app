<?php

namespace App\Filament\Widgets;

use App\Models\Medicine;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MedicineStockTable extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Medicine::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_obat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stok_obat')
                    ->label('Stock')
                    ->sortable()
                    ->color(fn (string $state): string => $state < 10 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('harga_obat')
                    ->money('IDR')
                    ->sortable(),
            ]);
    }
}
