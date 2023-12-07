<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;

use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Contracts\Support\Htmlable;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function getTabs():array {
        return[
            'all' => Tab::make('All'),
            'archived' => Tab::make('Archived')->modifyQueryUsing(function($query){
                return $query->onlyTrashed();
            })
        ];
    }

    protected function getHeaderActions(): array
    {

        return [
            Actions\CreateAction::make(),
        ];
    }
}
