<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\APIRequestResource\Pages;
use App\Filament\App\Resources\APIRequestResource\RelationManagers;
use App\Models\APIRequest;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class APIRequestResource extends Resource
{
    protected static ?string $model = APIRequest::class;

    protected static ?string $navigationIcon = 'heroicon-s-arrow-path-rounded-square';

    protected static ?string $navigationGroup = 'Requests';

    protected static ?string $recordTitleAttribute = 'requests';

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        return static::getModel()::query()->where('user_id', $user->id);
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('View Request')
                    ->description('These are the details of the request')
                    ->schema([
                        Forms\Components\TextInput::make('endpoint')
                            ->required()
                            ->label('end point'),
                        Forms\Components\TextInput::make('method')
                            ->required()
                            ->label('Methood'),
                        Forms\Components\TextInput::make('ip_address')
                            ->required()
                            ->label('IP Address'),
                        Forms\Components\TextInput::make('status')
                            ->required()
                            ->label('Status'),
                        Forms\Components\TextInput::make('created_at')
                            ->required()
                            ->label('created At'),
                        Forms\Components\TextInput::make('updated_at')
                            ->required()
                            ->label('updated At'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('endpoint')
                    ->copyable()
                    ->toggleable()
                    ->copyMessage('end point copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->label('End Point'),
                TextColumn::make('method')
                    ->copyable()
                    ->toggleable()
                    ->copyMessage('method copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->label('Methood'),
                TextColumn::make('ip_address')
                    ->copyable()
                    ->toggleable()
                    ->copyMessage('ip address copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->label('IP Address'),
                TextColumn::make('status')
                    ->copyable()
                    ->toggleable()
                    ->copyMessage('status copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->label('Status'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable()
                    ->sortable()
                    ->searchable()
                    ->label('created At'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable()
                    ->sortable()
                    ->searchable()
                    ->label('updated At'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'SUCCESS' => 'Completed',
                        'FAILED' => 'Failed',
                        'PENDING' => 'Pending',
                    ])
                    ->indicator('Status')
                    ->placeholder('Select Status')
                    ->searchable(),

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
                Tables\Actions\ViewAction::make(),
                // ExportAction::make()->exports([
                //     ExcelExport::make()


                // ]),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
                ExportBulkAction::make()
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
            'index' => Pages\ListAPIRequests::route('/'),
            'view' => Pages\ViewAPIRequest::route('/{record}')
            // 'create' => Pages\CreateAPIRequest::route('/create'),
            // 'edit' => Pages\EditAPIRequest::route('/{record}/edit'),
        ];
    }
}
