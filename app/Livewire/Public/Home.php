<?php

namespace App\Livewire\Public;

use App\Services\PropertyService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class Home extends Component
{
    public function render()
    {
        $properties = app(PropertyService::class);

        return view('livewire.public.home', [
            'recentProperties' => $properties->published()->latest()->take(6)->get(),
        ]);
    }
}
