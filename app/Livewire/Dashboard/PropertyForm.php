<?php

namespace App\Livewire\Dashboard;

use App\Models\Property;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PropertyForm extends Component
{
    public ?Property $property = null;

    public string $title = '';

    public string $description = '';

    public string $type = '';

    public string $currency = 'PKR';

    public string $price = '';

    public string $location = '';

    public string $available_from = '';

    public string $status = 'draft';

    public string $bedrooms = '';

    public string $bathrooms = '';

    public string $area_sqft = '';

    public function mount(?int $id = null): void
    {
        if (! $id) {
            return;
        }

        $this->property = auth()->user()->properties()->findOrFail($id);

        $this->title = $this->property->title;
        $this->description = (string) $this->property->description;
        $this->type = (string) $this->property->type;
        $this->currency = $this->property->currency ?? 'PKR';
        $this->price = (string) $this->property->price;
        $this->location = (string) $this->property->location;
        $this->available_from = $this->property->available_from?->format('Y-m-d') ?? '';
        $this->status = $this->property->status;
        $this->bedrooms = (string) ($this->property->details['bedrooms'] ?? '');
        $this->bathrooms = (string) ($this->property->details['bathrooms'] ?? '');
        $this->area_sqft = (string) ($this->property->details['area_sqft'] ?? '');
    }

    public function save(): void
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|in:apartment,house,land,commercial',
            'currency' => 'nullable|string|max:10',
            'price' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'available_from' => 'nullable|date',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'area_sqft' => 'nullable|integer|min:0',
        ];

        if ($this->property) {
            $rules['status'] = 'required|in:draft,published,archived';
        }

        $validated = $this->validate($rules);

        $attributes = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?: null,
            'type' => $validated['type'] ?: null,
            'currency' => $validated['currency'] ?: null,
            'price' => $validated['price'] !== '' && $validated['price'] !== null ? $validated['price'] : null,
            'location' => $validated['location'] ?: null,
            'available_from' => $validated['available_from'] ?: null,
            'details' => array_filter([
                'bedrooms' => $validated['bedrooms'] !== '' && $validated['bedrooms'] !== null ? (int) $validated['bedrooms'] : null,
                'bathrooms' => $validated['bathrooms'] !== '' && $validated['bathrooms'] !== null ? (int) $validated['bathrooms'] : null,
                'area_sqft' => $validated['area_sqft'] !== '' && $validated['area_sqft'] !== null ? (int) $validated['area_sqft'] : null,
            ], fn ($value) => $value !== null),
        ];

        if ($this->property) {
            $attributes['status'] = $validated['status'];
            $this->property->update($attributes);
        } else {
            auth()->user()->properties()->create($attributes);
        }

        $this->redirect(route('dashboard.properties.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.dashboard.property-form');
    }
}
