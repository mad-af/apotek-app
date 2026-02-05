<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Sales', 'Rp ' . number_format(Transaction::sum('total_amount'), 0, ',', '.')),
            Stat::make('Total Transactions', Transaction::count()),
        ];
    }
}
