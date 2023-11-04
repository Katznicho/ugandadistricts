<?php

namespace App\Filament\Resources\SubCountyResource\Pages;

use App\Filament\Resources\SubCountyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubCounty extends ViewRecord
{
    protected static string $resource = SubCountyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
