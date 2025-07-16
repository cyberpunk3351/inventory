<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Models\Device;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DatePicker::make('first_deployed_at')
                    ->label('First Deployed At')
                    ->maxDate(now())
                    ->native(false),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('health_lifecycle_value')
                            ->label('Health Lifecycle Value')
                            ->required()
                            ->numeric()
                            ->minValue(1),

                        Forms\Components\Select::make('health_lifecycle_unit')
                            ->label('Health Lifecycle Unit')
                            ->required()
                            ->options([
                                'day' => 'Day',
                                'month' => 'Month',
                                'year' => 'Year',
                            ])
                            ->default('year'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $search = request()->input('tableSearch');
                return $search
                    ? Device::search($search)->query()
                    : Device::query();
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Device Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('first_deployed_at')
                    ->label('First Deployed At')
                    ->date()
                    ->sortable()
                    ->placeholder('Not deployed'),

                BadgeColumn::make('device_health')
                    ->label('Device Health')
                    ->getStateUsing(fn (Device $record): string => $record->device_health)
                    ->colors([
                        'success' => 'Perfect',
                        'primary' => 'Good',
                        'warning' => 'Fair',
                        'danger' => 'Poor',
                        'secondary' => 'N/A',
                    ])
            ])
            ->filters([
                SelectFilter::make('device_health')
                    ->label('Device Health')
                    ->options([
                        'Perfect' => 'Perfect',
                        'Good' => 'Good',
                        'Fair' => 'Fair',
                        'Poor' => 'Poor',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value']) || !$data['value']) {
                            return $query;
                        }

                        $filter = addslashes($data['value']);
                        $deviceIds = Device::search($filter)->get()->pluck('id');

                        return $query->whereIn('id', $deviceIds);
                    }),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}
