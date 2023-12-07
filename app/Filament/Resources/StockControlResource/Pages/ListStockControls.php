<?php

namespace App\Filament\Resources\StockControlResource\Pages;

use App\Filament\Resources\StockControlResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockControls extends ListRecords
{
    protected static string $resource = StockControlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
