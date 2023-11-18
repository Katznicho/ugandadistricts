<?php

namespace App\Filament\Widgets;

use App\Models\APIRequest;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class APIRequestChart extends ChartWidget
{
    protected static ?string $heading = 'Requests Made';
    protected static string $color = 'success';

    protected static ?int $sort = 2;

    public ?string $filter = 'today';

    public function getDescription(): ?string
    {
        return 'The number of requests made per month';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }


    protected function getData(): array
    {


        $data = Trend::model(APIRequest::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Requests',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
