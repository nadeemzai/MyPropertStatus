<?php

namespace App\Filament\Agency\Resources\ConnectionResource\Pages;

use App\Filament\Agency\Resources\ConnectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConnections extends ListRecords
{
    protected static string $resource = ConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
