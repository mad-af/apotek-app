<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\SalesStats;
use App\Filament\Widgets\MedicineStockTable;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.reports';

    protected function getHeaderWidgets(): array
    {
        return [
            SalesStats::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            MedicineStockTable::class,
        ];
    }
}
