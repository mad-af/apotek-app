<?php

namespace App\Filament\Widgets;

use App\Models\Distributor;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Sales', 'Rp '.number_format(Transaction::sum('total_amount'), 0, ',', '.'))
                ->description('Total revenue from all transactions')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Transactions', (string) Transaction::count())
                ->description('Total number of transactions processed')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),
            Stat::make('Total Distributors', (string) Distributor::count())
                ->description('Active distributors in network')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),
        ];
    }
}
