<?php

namespace App\Filament\Resources\SubCountyResource\Pages;

use App\Filament\Resources\SubCountyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubCounty extends EditRecord
{
    protected static string $resource = SubCountyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
