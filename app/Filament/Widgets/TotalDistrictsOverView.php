<?php

namespace App\Filament\Widgets;

use App\Models\District;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalDistrictsOverView extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            //
            Stat::make('Total Districts', District::count())
                ->icon('heroicon-o-arrow-trending-up')
                ->description('Total number of districts')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route("filament.admin.resources.districts.index"))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),
            Stat::make('Total Counties', \App\Models\County::count())
                ->icon('heroicon-o-arrow-trending-up')
                ->description('Total number of counties')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route("filament.admin.resources.counties.index"))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),
            Stat::make('Total Sub Counties', \App\Models\SubCounty::count())
                ->icon('heroicon-o-arrow-trending-up')
                ->description('Total number of sub counties')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route("filament.admin.resources.sub-counties.index"))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),
            Stat::make('Total Parishes', \App\Models\Parish::count())
                ->icon('heroicon-o-arrow-trending-up')
                ->description('Total number of parishes')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route("filament.admin.resources.parishes.index"))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),
            Stat::make('Total Villages', \App\Models\Village::count())
                ->icon('heroicon-o-arrow-trending-up')
                ->description('Total number of villages')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                // ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route("filament.admin.resources.villages.index"))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),

        ];
    }
}
