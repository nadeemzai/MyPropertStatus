<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class MediaRelationManager extends RelationManager
{
    protected static string $relationship = 'media';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label('Preview')
                    ->disk('public')
                    ->height(60),
                Tables\Columns\TextColumn::make('type')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('caption')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        Storage::disk('public')->delete($record->path);
                    }),
            ]);
    }
}
