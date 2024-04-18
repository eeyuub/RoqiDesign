<?php

namespace App\Filament\Resources\StockControlResource\Pages;

use App\Filament\Resources\StockControlResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStockControl extends CreateRecord
{
    protected static string $resource = StockControlResource::class;

    protected function getRedirectUrl(): string
    {
    return $this->getResource()::getUrl('index');
    }
}
