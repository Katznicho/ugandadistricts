<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApiRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'apiRequests';



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('user_id')
                //     ->required()
                //     ->maxLength(255),
                Section::make(
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                )
                    ->description("This is the name of the user")
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user_id')
            ->columns([
                // Tables\Columns\TextColumn::make('user_id'),
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
                TextColumn::make('user_agent')
                    ->copyable()
                    ->toggleable()
                    ->copyMessage('user agent copied')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable()
                    ->label('User Agent'),
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
