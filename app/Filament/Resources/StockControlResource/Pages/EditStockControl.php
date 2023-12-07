<?php

namespace App\Filament\Resources\StockControlResource\Pages;

use App\Filament\Resources\StockControlResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockControl extends EditRecord
{
    protected static string $resource = StockControlResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
