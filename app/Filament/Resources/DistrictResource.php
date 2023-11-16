<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistrictResource\Pages;
use App\Filament\Resources\DistrictResource\RelationManagers;
use App\Models\County;
use App\Models\District;
use Carbon\Carbon;
use Filament\Actions\ActionGroup;
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
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Uganda Data';

    

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('View District')
                    ->description('These are the details of the district')
                    ->schema([
                        Forms\Components\TextInput::make('districtCode')
                            ->required()
                            ->numeric()
                            ->label('district Code'),
                        Forms\Components\TextInput::make('districtName')
                            ->required()
                            ->label('district Name'),
                    ])

            ]);
    }

    // public function getTableBulkActions()
    // {
    //     return  [
    //         ExportBulkAction::make()
    //     ];
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('districtCode')
                    ->copyable()
                    ->copyMessage('district code copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->label('district Code'),
                TextColumn::make('districtName')
                    ->copyable()
                    ->copyMessage('district name copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->label('district Name'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->label('created At'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->label('updated At'),
            ])
            ->filters([
                // Tables\Filters\TrashedFilter::make(),
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
                ]),
                ExportAction::make()->exports([
                    ExcelExport::make()


                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
                ExportBulkAction::make()
                // ->askForFilename()
                // ->askForWriterType(),



            ]);
    }

    public static function getRelations(): array
    {
        return [
            //

            RelationManagers\CountiesRelationManager::class,
            RelationManagers\SubcountiesRelationManager::class,
            RelationManagers\ParishesRelationManager::class,
            RelationManagers\VillagesRelationManager::class

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistricts::route('/'),
            'create' => Pages\CreateDistrict::route('/create'),
            'view' => Pages\ViewDistrict::route('/{record}'),
            'edit' => Pages\EditDistrict::route('/{record}/edit'),
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
