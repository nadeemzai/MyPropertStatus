<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ListingsRelationManager extends RelationManager
{
    protected static string $relationship = 'listings';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agency.name')
                    ->label('Agency'),
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
                Tables\Columns\TextColumn::make('agency_notes')
                    ->label('Notes')
                    ->placeholder('—')
                    ->limit(40),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
