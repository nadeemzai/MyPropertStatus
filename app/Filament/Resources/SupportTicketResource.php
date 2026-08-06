<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupportTicketResource\Pages;
use App\Models\SupportTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?string $navigationBadgeTooltip = 'Open tickets';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'open')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('status', 'open')->exists() ? 'danger' : null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('Submitted by')
                ->relationship('user', 'name')
                ->searchable()
                ->required()
                ->visible(fn (string $operation): bool => $operation === 'create'),
            Forms\Components\Placeholder::make('submitted_by')
                ->label('Submitted by')
                ->content(fn (?SupportTicket $record) => $record?->user
                    ? "{$record->user->name} ({$record->user->email})"
                    : '—')
                ->visible(fn (string $operation): bool => $operation === 'edit'),
            Forms\Components\TextInput::make('subject')
                ->required()
                ->maxLength(255)
                ->disabled(fn (string $operation): bool => $operation === 'edit'),
            Forms\Components\Textarea::make('message')
                ->required()
                ->columnSpanFull()
                ->disabled(fn (string $operation): bool => $operation === 'edit'),
            Forms\Components\Select::make('status')
                ->options([
                    'open' => 'Open',
                    'in_progress' => 'In Progress',
                    'resolved' => 'Resolved',
                    'closed' => 'Closed',
                ])
                ->default('open')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Submitted by')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'open' => 'danger',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'open' => 'Open',
                    'in_progress' => 'In Progress',
                    'resolved' => 'Resolved',
                    'closed' => 'Closed',
                ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupportTickets::route('/'),
            'create' => Pages\CreateSupportTicket::route('/create'),
            'edit' => Pages\EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
