<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Medicine;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\DatePicker::make('transaction_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('total_amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly(),
                    ])->columns(2),

                Forms\Components\Section::make('Transaction Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('medicine_id')
                                    ->label('Medicine')
                                    ->options(Medicine::query()->pluck('nama_obat', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get, Forms\Components\Component $component) {
                                        $medicine = Medicine::find($state);
                                        $price = 0;
                                        $stock = 0;

                                        if ($medicine) {
                                            $price = $medicine->harga_obat;
                                            $stock = $medicine->stok_obat;
                                            $set('price', $price);
                                            $set('stock', $stock);
                                        } else {
                                            $set('price', 0);
                                            $set('stock', 0);
                                        }

                                        $quantity = intval($get('quantity') ?? 1);
                                        $totalPrice = $price * $quantity;
                                        $set('total_price', $totalPrice);

                                        // Recalculate global total
                                        $items = $get('../../items');
                                        $currentUuid = last(explode('.', $component->getContainer()->getStatePath()));
                                        $total = 0;

                                        if (is_array($items)) {
                                            foreach ($items as $uuid => $item) {
                                                if ($uuid === $currentUuid) {
                                                    $total += $totalPrice;
                                                } else {
                                                    $itemPrice = floatval($item['price'] ?? 0);
                                                    $itemQty = intval($item['quantity'] ?? 0);
                                                    $total += $itemPrice * $itemQty;
                                                }
                                            }
                                        }
                                        $set('../../total_amount', $total);
                                    })
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->columnSpan(2), // Give medicine more space

                                Forms\Components\TextInput::make('stock')
                                    ->label('Stock')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->numeric()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live() // Live update with debounce
                                    ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set, Forms\Components\Component $component) {
                                        $price = floatval($get('price') ?? 0);
                                        $quantity = intval($state);
                                        $totalPrice = $quantity * $price;
                                        $set('total_price', $totalPrice);

                                        // Recalculate global total
                                        $items = $get('../../items');
                                        $currentUuid = last(explode('.', $component->getContainer()->getStatePath()));
                                        $total = 0;

                                        if (is_array($items)) {
                                            foreach ($items as $uuid => $item) {
                                                if ($uuid === $currentUuid) {
                                                    $total += $totalPrice;
                                                } else {
                                                    $itemPrice = floatval($item['price'] ?? 0);
                                                    $itemQty = intval($item['quantity'] ?? 0);
                                                    $total += $itemPrice * $itemQty;
                                                }
                                            }
                                        }
                                        $set('../../total_amount', $total);
                                    })
                                    ->rules([
                                        fn (Forms\Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $medicineId = $get('medicine_id');
                                            if ($medicineId) {
                                                $medicine = Medicine::find($medicineId);
                                                if ($medicine && $value > $medicine->stok_obat) {
                                                    $fail("Exceeds stock ({$medicine->stok_obat})");
                                                }
                                            }
                                        },
                                    ])
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('total_price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->columnSpan(1),
                            ])
                            ->columns(6) // Increased columns for better spacing
                            ->live()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $items = $get('items');
                                $total = 0;
                                if ($items) {
                                    foreach ($items as $item) {
                                        $total += $item['total_price'] ?? 0;
                                    }
                                }
                                $set('total_amount', $total);
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Print Receipt')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Transaction $record) => route('transactions.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
        ];
    }
}
