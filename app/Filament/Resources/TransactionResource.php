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
                Forms\Components\DatePicker::make('transaction_date')
                    ->required()
                    ->default(now()),

                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('medicine_id')
                            ->label('Medicine')
                            ->options(Medicine::query()->pluck('nama_obat', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $medicine = Medicine::find($state);
                                if ($medicine) {
                                    $set('price', $medicine->harga_obat);
                                    $set('stock', $medicine->stok_obat);
                                }
                            })
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                        Forms\Components\TextInput::make('stock')
                            ->label('Stock Available')
                            ->disabled()
                            ->dehydrated(false)
                            ->numeric(),

                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                $price = $get('price');
                                $set('total_price', $state * $price);
                            })
                            ->rules([
                                fn (Forms\Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $medicineId = $get('medicine_id');
                                    if ($medicineId) {
                                        $medicine = Medicine::find($medicineId);
                                        if ($medicine && $value > $medicine->stok_obat) {
                                            $fail("Quantity exceeds available stock ({$medicine->stok_obat}).");
                                        }
                                    }
                                },
                            ]),

                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly(),

                        Forms\Components\TextInput::make('total_price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly(),
                    ])
                    ->columns(5)
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

                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->readOnly(),
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
                Tables\Actions\EditAction::make(),
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
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
