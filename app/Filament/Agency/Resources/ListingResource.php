<?php

namespace App\Filament\Agency\Resources;

use App\Filament\Agency\Resources\ListingResource\Pages;
use App\Filament\Agency\Resources\ListingResource\RelationManagers;
use App\Models\Listing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\KeyValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
             ->schema([
                Forms\Components\Select::make('property_id')
                    ->relationship('property', 'title') // change `name` if your Property model uses a different column
                    ->required()
                    ->searchable(),

                Forms\Components\Select::make('agency_id')
                    ->relationship('agency', 'name') // adjust column
                    ->required()
                    ->searchable(),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'available' => 'Available',
                        'rented' => 'Rented',
                        'sold' => 'Sold',
                        'archived' => 'Archived',
                    ])
                    ->default('pending')
                    ->required(),

                Forms\Components\Toggle::make('agency_proposed')
                    ->default(false),

                Forms\Components\Toggle::make('user_approved')
                    ->label('User Approved')
                    ->nullable(),

                Forms\Components\DateTimePicker::make('approved_at')
                    ->label('Approved At')
                    ->nullable(),

                Forms\Components\Textarea::make('agency_notes')
                    ->label('Agency Notes')
                    ->columnSpanFull(),

                KeyValue::make('meta')
                    ->label('Meta Information')
                    ->reorderable()
                    ->addButtonLabel('Add meta data')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('property.name')->label('Property')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('agency.name')->label('Agency')->sortable()->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'available',
                        'danger' => 'rented',
                        'primary' => 'sold',
                        'gray' => 'archived',
                    ])
                    ->sortable(),
                Tables\Columns\IconColumn::make('agency_proposed')->boolean(),
                Tables\Columns\IconColumn::make('user_approved')->boolean(),
                Tables\Columns\TextColumn::make('approved_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'available' => 'Available',
                        'rented' => 'Rented',
                        'sold' => 'Sold',
                        'archived' => 'Archived',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListListings::route('/'),
            'create' => Pages\CreateListing::route('/create'),
            'edit' => Pages\EditListing::route('/{record}/edit'),
        ];
    }
}
