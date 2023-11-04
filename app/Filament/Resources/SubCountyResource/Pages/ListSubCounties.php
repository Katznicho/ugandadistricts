<?php

namespace App\Filament\Resources\SubCountyResource\Pages;

use App\Filament\Resources\SubCountyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubCounties extends ListRecords
{
    protected static string $resource = SubCountyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
