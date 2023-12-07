<?php

declare(strict_types=1);

namespace App\Filament\Resources\SupplierResource\Schema;

use App\Models\Supplier;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;

final class supplierSchema
{
    private function __construct()
    {
    }

    public static function schema(): array
    {
        // $timezones = Timezone::generateList();

        return [
            TextInput::make('name')->type('text'),
            TextInput::make('address')->type('text'),
            TextInput::make('phone')->type('tel'),
            TextInput::make('contactPerson')->type('text'),
            TextInput::make('city')->type('text'),
            TextInput::make('note')->type('text'),
            Select::make('isActive')
            ->options(isActive::class)
            ->native(false)
        ];
    }

    /* public static function createForm(
        Form $form,

    ): Form {
        return $form->schema([
            TextInput::make('name')->type('text'),
            TextInput::make('address')->type('text'),
            TextInput::make('phone')->type('tel'),
            TextInput::make('contactPerson')->type('text'),
            TextInput::make('city')->type('text'),
            TextInput::make('note')->type('text'),
            Select::make('isActive')
            ->options(isActive::class)
            ->native(false)

        ]);

    }

    public static function supplierItems(): array
    {
        return [
            TextInput::make('name')->type('text'),
            TextInput::make('address')->type('text'),
            TextInput::make('phone')->type('tel'),
            TextInput::make('contactPerson')->type('text'),
            TextInput::make('city')->type('text'),
            TextInput::make('note')->type('text'),
            Select::make('isActive')
            ->options(isActive::class)
            ->native(false)
        ];
    } */



}
