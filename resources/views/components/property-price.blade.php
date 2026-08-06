@props(['property'])

<span {{ $attributes }}>
    @if ($property->price)
        {{ $property->currency ?? 'PKR' }} {{ number_format($property->price) }}
    @else
        Price on request
    @endif
</span>
