<?php

namespace App\Filament\Agency\Resources\ListingResource\Pages;

use App\Filament\Agency\Resources\ListingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateListing extends CreateRecord
{
    protected static string $resource = ListingResource::class;
}
