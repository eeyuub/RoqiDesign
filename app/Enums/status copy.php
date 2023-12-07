<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Status: string implements HasLabel
{
    case PENDING = 'pending';
    case PREPARING = 'preparing';
    case IN_QUEUE = 'in-queue';
    case READY = 'ready';
    case DISPATCHED = 'dispatched';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
    case FAILED = 'failed';

    /* public function getColor(): string|array|null
    {
        return match ($this) {
            self::COMPLETED => 'success',
            self::READY, self::PREPARING, self::IN_QUEUE , self::DISPATCHED => 'warning',
            self::CANCELED, self::FAILED => 'danger',
            default => 'info',
        };
    } */

    /* public function getLabel(): ?string
    {
        return Str::headline($this->value);
    } */

    public function getLabel(): ?string
    {
        return $this->name;

        // or

        return match ($this) {
            self::PENDING => 'pending',
            self::PREPARING => 'preparing',
            self::IN_QUEUE => 'In-Queue',
            self::READY => 'Ready',
            self::DISPATCHED => 'Dispatched',
            self::COMPLETED => 'Completed',
            self::CANCELED => 'Canceled',
            self::FAILED => 'Failed',
        };
    }

    /* public function getIcon(): ?string
    {
        return match ($this) {
            self::COMPLETED => 'heroicon-o-check-circle',
            self::READY, self::PREPARING, self::IN_QUEUE , self::DISPATCHED => 'heroicon-o-clock',
            self::CANCELED, self::FAILED => 'heroicon-o-x-circle',
            default => 'heroicon-o-information-circle',
        };
    } */
}
