<?php

namespace App\Filament\Agency\Resources\ListingResource\Pages;

use App\Exceptions\ListingActionException;
use App\Filament\Agency\Resources\ListingResource;
use App\Models\Listing;
use App\Services\ListingService;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;

class CreateListing extends CreateRecord
{
    protected static string $resource = ListingResource::class;

    protected function handleRecordCreation(array $data): Listing
    {
        try {
            return app(ListingService::class)->propose(
                Filament::auth()->user(),
                $data['property_id'],
                $data['agency_notes'] ?? null
            );
        } catch (ListingActionException $e) {
            Notification::make()->title($e->getMessage())->danger()->send();

            throw new Halt();
        }
    }
}
