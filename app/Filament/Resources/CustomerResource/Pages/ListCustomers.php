<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    public function getTabs():array {
        return[
            'all' => Tab::make('All'),
            'activeCustomer' => Tab::make('Active')->modifyQueryUsing(function($query){
                return $query->where('isActive',true);
            }),
            'inactiveCustomer' => Tab::make('Inactive')->modifyQueryUsing(function($query){
                return $query->where('isActive',false);
            }),
            'archivedCustomer' => Tab::make('Archived')->modifyQueryUsing(function($query){
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
