<?php

namespace App\Filament\Agency\Resources;

use App\Exceptions\ListingActionException;
use App\Filament\Agency\Resources\ListingResource\Pages;
use App\Models\Listing;
use App\Models\Property;
use App\Services\ListingService;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    /**
     * Agencies share this panel, so every query must be scoped to the
     * logged-in agency's own listings.
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
                ->options(fn () => Property::query()->limit(50)->pluck('title', 'id'))
                ->getSearchResultsUsing(fn (string $search) => Property::query()
                    ->where('title', 'like', "%{$search}%")
                    ->limit(50)
                    ->pluck('title', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\Textarea::make('agency_notes')
                ->label('Notes to property owner')
                ->maxLength(2000)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->searchable(),
                Tables\Columns\TextColumn::make('property.location')
                    ->label('Location'),
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
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'available' => 'Available',
                    'rented' => 'Rented',
                    'sold' => 'Sold',
                    'archived' => 'Archived',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (Listing $record) => (bool) $record->user_approved)
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'available' => 'Available',
                                'rented' => 'Rented',
                                'sold' => 'Sold',
                                'archived' => 'Archived',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('reason'),
                    ])
                    ->action(function (Listing $record, array $data) {
                        try {
                            app(ListingService::class)->updateStatus(
                                $record,
                                Filament::auth()->user(),
                                $data['status'],
                                $data['reason'] ?? null
                            );

                            Notification::make()->title('Listing status updated')->success()->send();
                        } catch (ListingActionException $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListListings::route('/'),
            'create' => Pages\CreateListing::route('/create'),
            'view' => Pages\ViewListing::route('/{record}'),
        ];
    }
}
