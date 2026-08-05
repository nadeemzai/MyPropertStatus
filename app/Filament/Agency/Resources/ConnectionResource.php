<?php

namespace App\Filament\Agency\Resources;

use App\Filament\Agency\Resources\ConnectionResource\Pages;
use App\Models\Connection;
use App\Models\Property;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConnectionResource extends Resource
{
    protected static ?string $model = Connection::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    /**
     * Agencies share this panel, so every query must be scoped to the
     * logged-in agency's own connections.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('agency_id', Filament::auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('property_id')
                ->label('Property')
                ->helperText('Leave blank if reaching out to a phone number that isn\'t a registered property owner yet.')
                ->options(fn () => Property::query()->limit(50)->pluck('title', 'id'))
                ->getSearchResultsUsing(fn (string $search) => Property::query()
                    ->where('title', 'like', "%{$search}%")
                    ->limit(50)
                    ->pluck('title', 'id'))
                ->searchable()
                ->live()
                ->requiredWithout('target_phone'),
            Forms\Components\TextInput::make('target_phone')
                ->label('Target phone')
                ->tel()
                ->maxLength(255)
                ->live()
                ->requiredWithout('property_id'),
            Forms\Components\Textarea::make('message')
                ->maxLength(2000)
                ->columnSpanFull(),
            Forms\Components\DateTimePicker::make('expires_at')
                ->label('Expires at')
                ->helperText('Optional. Leave blank if this request should not expire.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('target_phone')
                    ->label('Target Phone')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'expired' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'accepted' => 'Accepted',
                    'rejected' => 'Rejected',
                    'expired' => 'Expired',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConnections::route('/'),
            'create' => Pages\CreateConnection::route('/create'),
            'view' => Pages\ViewConnection::route('/{record}'),
        ];
    }
}
