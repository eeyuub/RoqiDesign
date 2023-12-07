<?php

namespace App\Filament\Resources\DepenseResource\Pages;

use App\Filament\Resources\DepenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab ;

class ListDepenses extends ListRecords
{
    protected static string $resource = DepenseResource::class;

    public function getTabs():array {
        return[
            'all' => Tab::make('All'),
            'archivedDepenses' => Tab::make('Archived')->modifyQueryUsing(function($query){
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
