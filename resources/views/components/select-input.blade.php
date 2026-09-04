@props(['disabled' => false])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-700 sm:text-sm']) }}>
    {{ $slot }}
</select>
