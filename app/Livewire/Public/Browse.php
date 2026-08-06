<?php

namespace App\Livewire\Public;

use App\Services\PropertyService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.public')]
class Browse extends Component
{
    use WithPagination;

    #[Url]
    public string $location = '';

    #[Url]
    public string $type = '';

    #[Url]
    public string $min_price = '';

    #[Url]
    public string $max_price = '';

    public function updated(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $properties = app(PropertyService::class);

        $query = $properties->filter($properties->published(), [
            'location' => $this->location,
            'type' => $this->type,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
        ]);

        return view('livewire.public.browse', [
            'properties' => $query->latest()->paginate(9),
        ]);
    }
}
