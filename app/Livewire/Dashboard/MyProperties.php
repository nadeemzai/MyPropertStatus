<?php

namespace App\Livewire\Dashboard;

use App\Services\PropertyService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MyProperties extends Component
{
    use WithPagination;

    #[Url]
    public string $tab = 'active';

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function delete(int $propertyId): void
    {
        auth()->user()->properties()->findOrFail($propertyId)->delete();
    }

    public function render()
    {
        $properties = app(PropertyService::class);

        $query = $properties->byTab($properties->mine(auth()->user()), $this->tab);

        return view('livewire.dashboard.my-properties', [
            'properties' => $query->latest()->paginate(9),
        ]);
    }
}
