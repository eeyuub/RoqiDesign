<?php

namespace App\Enums;
use Filament\Support\Contracts\HasLabel;


enum payment:string  implements HasLabel{

    case CreditCard = 'creditCard';
    case BankTransfer = 'bankTransfer';
    case Cash = 'cash';
    case Cheque = 'cheque';
    case PartialPayment = 'partialPayment';

    public function getLabel(): ?string
    {
        return $this->name;

        // or

        return match ($this) {
            self::CreditCard => 'CreditCard',
            self::BankTransfer => 'BankTransfer',
            self::Cash => 'Cash',
            self::Cheque => 'Cheque',
            self::PartialPayment => 'PartialPayment',
        };
    }
}
