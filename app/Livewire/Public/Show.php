<?php

namespace App\Livewire\Public;

use App\Models\Property;
use App\Services\PropertyService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class Show extends Component
{
    public Property $property;

    public function mount($id): void
    {
        $this->property = app(PropertyService::class)->published()->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.public.show');
    }
}
