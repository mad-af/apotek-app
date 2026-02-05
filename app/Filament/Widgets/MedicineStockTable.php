<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\MedicineResource;
use App\Models\Medicine;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MedicineStockTable extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Low Stock Medicines';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Medicine::query()->where('stok_obat', '<=', 10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_obat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stok_obat')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => $state <= 10 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('harga_obat')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->recordUrl(
                fn (Medicine $record): string => MedicineResource::getUrl('edit', ['record' => $record]),
            );
    }
}
