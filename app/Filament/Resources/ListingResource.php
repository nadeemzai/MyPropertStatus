<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListingResource\Pages;
use App\Models\Listing;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('agency.name')
                    ->label('Agency')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'available' => 'success',
                        'rented', 'sold' => 'info',
                        'archived' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('user_approved')
                    ->label('Owner Approved')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'available' => 'Available',
                    'rented' => 'Rented',
                    'sold' => 'Sold',
                    'archived' => 'Archived',
                ]),
                Tables\Filters\SelectFilter::make('agency')
                    ->relationship('agency', 'name')
                    ->searchable(),
                Tables\Filters\TernaryFilter::make('user_approved')
                    ->label('Owner Approved'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TextEntry::make('property.title')->label('Property'),
            TextEntry::make('property.location')->label('Location')->placeholder('—'),
            TextEntry::make('agency.name')->label('Agency'),
            TextEntry::make('status')->badge(),
            IconEntry::make('user_approved')->label('Owner Approved')->boolean(),
            TextEntry::make('agency_notes')->label('Agency Notes')->placeholder('—')->columnSpanFull(),
            TextEntry::make('approved_at')->dateTime()->placeholder('—'),
            TextEntry::make('created_at')->dateTime(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListListings::route('/'),
            'view' => Pages\ViewListing::route('/{record}'),
        ];
    }
}
