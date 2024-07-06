<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Produk', fn () => Product::withoutTrashed()->count())
                ->icon('heroicon-o-beaker')
                ->url('products'),
        ];
    }
}
