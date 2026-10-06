<?php

namespace App\Filament\Widgets;

use App\Models\Consignor;
use App\Models\Item;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    // Determines the order of the widget on the dashboard
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Find how many items have 10 or fewer pieces in stock
        $lowStockCount = Item::withSum('transactions', 'quantity')
            ->get()
            ->where('transactions_sum_quantity', '<=', 10)
            ->count();

        return [
            Stat::make('Active Consignors', Consignor::count())
                ->description('Total registered suppliers')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),

            Stat::make('Medical Supplies Catalog', Item::count())
                ->description('Total unique items tracked')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('success'),

            Stat::make('Low Stock Alerts', $lowStockCount)
                ->description('Items with 10 or less stock')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),
        ];
    }
}