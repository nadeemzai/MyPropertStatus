<?php

namespace App\Filament\Resources\AgencyResource\Pages;

use App\Filament\Resources\AgencyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Form;

class CreateAgency extends CreateRecord
{
    protected static string $resource = AgencyResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form); // or customize
    }
}
