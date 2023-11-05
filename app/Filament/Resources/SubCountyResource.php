<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubCountyResource\Pages;
use App\Filament\Resources\SubCountyResource\RelationManagers;
use App\Models\SubCounty;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubCountyResource extends Resource
{
    protected static ?string $model = SubCounty::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Uganda Data';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Section::make('View Sub County')
                ->description('These are the details of the sub county')
                ->schema([
                    Forms\Components\TextInput::make('subCountyCode')
                        ->required()
                        ->numeric()
                        ->label('Sub County Code'),
                    Forms\Components\TextInput::make('subCountyName')
                        ->required()
                        ->label('Sub County Name'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('districtCode')
                    ->copyable()
                    ->copyMessage('district code copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('District Code'),
                TextColumn::make('district.districtName')
                    ->copyable()
                    ->copyMessage('district name copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('District Name'),
                TextColumn::make('countyCode')
                    ->copyable()
                    ->copyMessage('county code copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('County Code'),
                TextColumn::make('county.countyName')
                    ->copyable()
                    ->copyMessage('county name copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('County Name'),
                TextColumn::make('subCountyCode')
                    ->copyable()
                    ->copyMessage('sub county code copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('Sub County Code'),
                TextColumn::make('subCountyName')
                    ->copyable()
                    ->copyMessage('sub county name copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('Sub County Name'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->searchable()
                    ->label('Created At'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->label('Updated At')
            ])
            ->filters([
                //Tables\Filters\TrashedFilter::make(),
                Filter::make('created_at')
                ->form([
                    DatePicker::make('created_from'),
                    DatePicker::make('created_until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['from'] ?? null) {
                        $indicators[] = Indicator::make('Created from ' . Carbon::parse($data['from'])->toFormattedDateString())
                            ->removeField('from');
                    }

                    if ($data['until'] ?? null) {
                        $indicators[] = Indicator::make('Created until ' . Carbon::parse($data['until'])->toFormattedDateString())
                            ->removeField('until');
                    }

                    return $indicators;
                })
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\DistrictRelationManager::class,
            RelationManagers\CountyRelationManager::class,
            RelationManagers\ParishesRelationManager::class,
            RelationManagers\VillagesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubCounties::route('/'),
            'create' => Pages\CreateSubCounty::route('/create'),
            'view' => Pages\ViewSubCounty::route('/{record}'),
            'edit' => Pages\EditSubCounty::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
