@props(['property'])

@php($items = collect([
    isset($property->details['bedrooms']) ? $property->details['bedrooms'].' bed' : null,
    isset($property->details['bathrooms']) ? $property->details['bathrooms'].' bath' : null,
    isset($property->details['area_sqft']) ? $property->details['area_sqft'].' sqft' : null,
])->filter())

@if ($items->isNotEmpty())
    <p {{ $attributes->merge(['class' => 'text-sm text-gray-500']) }}>{{ $items->join(' · ') }}</p>
@endif
