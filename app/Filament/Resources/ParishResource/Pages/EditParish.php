<?php

namespace App\Filament\Resources\ParishResource\Pages;

use App\Filament\Resources\ParishResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParish extends EditRecord
{
    protected static string $resource = ParishResource::class;

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
