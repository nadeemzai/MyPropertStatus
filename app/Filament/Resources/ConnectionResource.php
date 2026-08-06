<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConnectionResource\Pages;
use App\Models\Connection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConnectionResource extends Resource
{
    protected static ?string $model = Connection::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'Properties';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->placeholder('—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('target_phone')
                    ->label('Target Phone')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('agency.name')
                    ->label('Agency')
                    ->searchable(),
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'accepted' => 'Accepted',
                    'rejected' => 'Rejected',
                    'expired' => 'Expired',
                ]),
                Tables\Filters\SelectFilter::make('agency')
                    ->relationship('agency', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TextEntry::make('property.title')->label('Property')->placeholder('—'),
            TextEntry::make('target_phone')->label('Target Phone')->placeholder('—'),
            TextEntry::make('agency.name')->label('Agency'),
            TextEntry::make('status')->badge(),
            TextEntry::make('message')->placeholder('—')->columnSpanFull(),
            TextEntry::make('expires_at')->dateTime()->placeholder('—'),
            TextEntry::make('created_at')->dateTime(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConnections::route('/'),
            'view' => Pages\ViewConnection::route('/{record}'),
        ];
    }
}
