<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Distributor;

class DistributorMap extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static string $view = 'filament.pages.distributor-map';

    protected function getViewData(): array
    {
        return [
            'distributors' => Distributor::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get()
        ];
    }
}
