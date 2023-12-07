<?php

namespace App\Enums;
use Filament\Support\Contracts\HasLabel;


enum customerGender:string  implements HasLabel{
    case Male = 'male';
    case Female = 'female';

    public function getLabel(): ?string
    {
        return $this->name;

        // or

        return match ($this) {
            self::Male => 'Male',
            self::Female => 'Female',
        };
    }
}
