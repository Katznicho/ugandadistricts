<?php

namespace App\Filament\Widgets;

use App\Models\APIRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class APIRequestOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Requests', APIRequest::count())
                ->icon('heroicon-s-arrow-path-rounded-square')
                ->description('Total number of requests')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route("filament.app.resources.a-p-i-requests.index"))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),

            Stat::make('Completed Requests', APIRequest::where([

                'status' => config("status.SUCCESS"),

            ])->count())
                ->icon('heroicon-s-arrow-path-rounded-square')
                ->description('Total number of completed requests')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route("filament.app.resources.a-p-i-requests.index"))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),

            Stat::make('Failed Requests', APIRequest::where([

                'status' => config("status.FAILED"),
            ])->count())
                ->icon('heroicon-s-arrow-path-rounded-square')
                ->description('Total number of failed requests')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger')
                ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route("filament.app.resources.a-p-i-requests.index"))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),

            Stat::make('Pending Requests', APIRequest::where(
                [

                    'status' => config("status.PENDING"),
                ]
            )->count())
                ->icon('heroicon-s-arrow-path-rounded-square')
                ->description('Total number of pending requests')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning')
                ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route("filament.app.resources.a-p-i-requests.index"))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),
        ];
    }
}
