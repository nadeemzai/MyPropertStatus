<?php

namespace App\Filament\Agency\Resources\ConnectionResource\Pages;

use App\Filament\Agency\Resources\ConnectionResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateConnection extends CreateRecord
{
    protected static string $resource = ConnectionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['agency_id'] = Filament::auth()->id();
        $data['status'] = 'pending';

        return $data;
    }
}
