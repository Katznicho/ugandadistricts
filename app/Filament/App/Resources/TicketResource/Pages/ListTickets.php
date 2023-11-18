<?php

namespace App\Filament\App\Resources\TicketResource\Pages;

use App\Filament\App\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'open' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'open'))
                ->icon('heroicon-o-lock-open')
                ->badge(fn (Builder $query) => $query->where('status', 'open')->count())
                ->badgeColor('danger'),
            'in progress' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'in progress'))
                ->icon('heroicon-o-clock')
                ->badge(fn (Builder $query) => $query->where('status', 'in progress')->count())
                ->badgeColor('warning'),
            'closed' => Tab::make()
                ->icon('heroicon-o-lock-closed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'closed'))
                ->badge(fn (Builder $query) => $query->where('status', 'closed')->count())
                ->badgeColor('success')
        ];
    }
}
