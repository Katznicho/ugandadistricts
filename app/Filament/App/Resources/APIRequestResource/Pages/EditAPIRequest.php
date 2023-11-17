<?php

namespace App\Filament\App\Resources\APIRequestResource\Pages;

use App\Filament\App\Resources\APIRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAPIRequest extends EditRecord
{
    protected static string $resource = APIRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
