<?php

namespace App\Filament\Agency\Resources;

use App\Filament\Agency\Resources\AgencyApiKeyResource\Pages;
use App\Models\AgencyApiKey;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class AgencyApiKeyResource extends Resource
{
    protected static ?string $model = AgencyApiKey::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'API Keys';

    protected static ?string $slug = 'api-keys';

    public static function getModelLabel(): string
    {
        return 'API key';
    }

    public static function getPluralModelLabel(): string
    {
        return 'API Keys';
    }

    /**
     * Agencies share this panel, so every query must be scoped to the
     * logged-in agency's own keys.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('agency_id', Filament::auth()->id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('api_key')
                    ->label('Key')
                    ->formatStateUsing(fn (string $state) => Str::mask($state, '•', 0, -4)),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('generate')
                    ->label('Generate new key')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Forms\Components\TextInput::make('label')
                            ->maxLength(255)
                            ->helperText('Optional. Helps you tell keys apart later, e.g. "Production server".'),
                    ])
                    ->action(function (array $data) {
                        $plainKey = Str::random(64);

                        AgencyApiKey::create([
                            'agency_id' => Filament::auth()->id(),
                            'api_key' => $plainKey,
                            'label' => $data['label'] ?? null,
                            'is_active' => true,
                        ]);

                        Notification::make()
                            ->title('API key generated')
                            ->body("Copy this key now — it will not be shown again:\n\n{$plainKey}")
                            ->success()
                            ->persistent()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('rename')
                    ->label('Rename')
                    ->icon('heroicon-o-pencil')
                    ->form([
                        Forms\Components\TextInput::make('label')->maxLength(255),
                    ])
                    ->fillForm(fn (AgencyApiKey $record) => ['label' => $record->label])
                    ->action(fn (AgencyApiKey $record, array $data) => $record->update(['label' => $data['label'] ?? null])),
                Tables\Actions\Action::make('toggleActive')
                    ->label(fn (AgencyApiKey $record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn (AgencyApiKey $record) => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                    ->color(fn (AgencyApiKey $record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (AgencyApiKey $record) => $record->update(['is_active' => ! $record->is_active])),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgencyApiKeys::route('/'),
        ];
    }
}
