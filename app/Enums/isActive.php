<?php

namespace App\Enums;
use Filament\Support\Contracts\HasLabel;


enum isActive:string  implements HasLabel{

    case Active = '1';
    case Inactive = '0';

    public function getLabel(): ?string
    {
        return $this->name;

        // or

        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
        };
    }
}
