<?php

namespace App\Filament\Resources\ParishResource\Pages;

use App\Filament\Resources\ParishResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParishes extends ListRecords
{
    protected static string $resource = ParishResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
