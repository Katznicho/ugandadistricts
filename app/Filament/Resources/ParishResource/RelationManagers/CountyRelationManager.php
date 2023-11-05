<?php

namespace App\Filament\Resources\ParishResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CountyRelationManager extends RelationManager
{
    protected static string $relationship = 'county';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('countyCode')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('countyCode')
            ->columns([
                Tables\Columns\TextColumn::make('countyCode')
                ->sortable()
                ->searchable()
                ->toggleable()
                ->label(' County Code')
                ,
            Tables\Columns\TextColumn::make('countyName')
                ->sortable()
                ->searchable()
                ->toggleable()
                ->label('County Name')
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
