<?php

namespace App\Filament\Resources\SubCountyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VillagesRelationManager extends RelationManager
{
    protected static string $relationship = 'villages';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('subCountyCode')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subCountyCode')
            ->columns([
                Tables\Columns\TextColumn::make('villageCode')
                ->sortable()
                ->searchable()
                ->toggleable()
                ->label('Village Code')
                ,
            Tables\Columns\TextColumn::make('villageName')
                ->sortable()
                ->searchable()
                ->toggleable()
                ->label('Village Name')
                ,
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->searchable()
                ->toggleable()
                ->label('created At'),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->searchable()
                ->toggleable()
                ->label('updated At'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
