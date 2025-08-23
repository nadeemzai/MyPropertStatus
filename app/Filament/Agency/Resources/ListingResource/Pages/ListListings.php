<?php

namespace App\Filament\Agency\Resources\ListingResource\Pages;

use App\Filament\Agency\Resources\ListingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListListings extends ListRecords
{
    protected static string $resource = ListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
