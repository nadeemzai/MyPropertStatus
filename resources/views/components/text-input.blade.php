@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm']) }}>
