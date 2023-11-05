<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VillageResource\Pages;
use App\Filament\Resources\VillageResource\RelationManagers;
use App\Models\County;
use App\Models\Parish;
use App\Models\SubCounty;
use App\Models\Village;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;


class VillageResource extends Resource
{
    protected static ?string $model = Village::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Uganda Data';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Village Form')
                    ->description('Add  a new village')
                    ->schema([
                        //select a district
                        Forms\Components\Select::make('districtCode')
                            ->placeholder("Please select a district")
                            ->relationship('district', 'districtName')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->debounce(100)
                            ->afterStateUpdated(
                                function (callable $set) {
                                    $set('countyCode', null);
                                    $set('subCountyCode', null);
                                    $set('parishCode', null);
                                }
                            )
                            ->required()
                            ->label('District'),
                        //once a district is selected select a county
                        Forms\Components\Select::make('countyCode')
                            ->placeholder("Please select a county")
                            ->options(
                                fn (Get $get): Collection => County::query()
                                    ->where('districtCode', $get('districtCode'))
                                    ->pluck('countyName', 'countyCode')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(
                                function (callable $set) {
                                    $set('subCountyCode', null);
                                    $set('parishCode', null);
                                }
                            )
                            ->label('County'),
                        //once a county is selected select a subcounty based on district and county
                        Forms\Components\Select::make('subCountyCode')
                            ->placeholder("Please select a sub county")
                            ->options(
                                fn (Get $get): Collection => SubCounty::query()
                                    ->where('districtCode', $get('districtCode'))
                                    ->where('countyCode', $get('countyCode'))
                                    ->pluck('subCountyName', 'subCountyCode')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(
                                // fn (callable $set) => $set('parishCode', null)
                                function (callable $set) {
                                    $set('parishCode', null);
                                }
                            )
                            ->label('Sub County'),
                        // once a subcounty is selected select a parish based on district, county and subcounty
                        Forms\Components\Select::make('parishCode')
                            ->placeholder("Please select a parish")
                            ->options(
                                fn (Get $get): Collection => Parish::query()
                                    ->where('districtCode', $get('districtCode'))
                                    ->where('countyCode', $get('countyCode'))
                                    ->where('subCountyCode', $get('subCountyCode'))
                                    ->pluck('parishName', 'parishCode')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->label('Parish'),
                        //create in putfor the village code and the code should be unique and it shouldnt in the database
                        // Create an input for the village code
                        Forms\Components\TextInput::make('villageCode')
                            ->required()
                            ->unique('villages', 'villageCode')
                            ->label('Village Code'),

                        //create an input for the village
                        Forms\Components\TextInput::make('villageName')
                            ->required()
                            ->maxLength(255)
                            ->label('Village Name')


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
                    ->label('district Code'),
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
                TextColumn::make('subcounty.subCountyName')
                    ->copyable()
                    ->copyMessage('sub county name copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('parishCode')
                    ->copyable()
                    ->copyMessage('parish code copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('Parish Code'),
                TextColumn::make('parish.parishName')
                    ->copyable()
                    ->copyMessage('parish name copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label("Parish Name"),
                TextColumn::make('villageCode')
                    ->copyable()
                    ->copyMessage('village code copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('Village Code'),
                TextColumn::make('villageName')
                    ->copyable()
                    ->copyMessage('village name copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('Village Name'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->label('created At'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->searchable()
                    ->label('updated At')
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVillages::route('/'),
            'create' => Pages\CreateVillage::route('/create'),
            'view' => Pages\ViewVillage::route('/{record}'),
            'edit' => Pages\EditVillage::route('/{record}/edit'),
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
