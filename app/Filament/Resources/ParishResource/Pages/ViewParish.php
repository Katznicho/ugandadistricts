<?php

namespace App\Filament\Resources\ParishResource\Pages;

use App\Filament\Resources\ParishResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewParish extends ViewRecord
{
    protected static string $resource = ParishResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
