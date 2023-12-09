<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAdminOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Stat::make('Commandes', Order::count()),
            Stat::make('Total Commandes', number_format((float)Order::sum('totalAmount'), 2, '.', '') . ' DH')
            ->description('IN')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success'),
            Stat::make('Total Achats', number_format((float)Purchase::sum('totalAmount'), 2, '.', '') . ' DH')
            ->description('OUT')
            ->descriptionIcon('heroicon-m-arrow-trending-down')
            ->color('danger'),
            Stat::make('Fournisseur', Supplier::count()),
            Stat::make('Client',Customer::count()),

        ];
    }
}
