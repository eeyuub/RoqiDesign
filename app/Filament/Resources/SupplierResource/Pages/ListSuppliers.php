<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;

use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab ;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;


    public function getTabs():array {
        return[
            'all' => Tab::make('All'),
            'activeSuppliers' => Tab::make('Active')->modifyQueryUsing(function($query){
                return $query->where('isActive',true);
            }),
            'inactiveSuppliers' => Tab::make('Inactive')->modifyQueryUsing(function($query){
                return $query->where('isActive',false);
            }),
            'archivedSuppliers' => Tab::make('Archived')->modifyQueryUsing(function($query){
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
