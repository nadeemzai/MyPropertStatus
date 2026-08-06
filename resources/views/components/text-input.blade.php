@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm outline-none focus:border-green-700 focus:ring-2 focus:ring-green-700 sm:text-sm']) }}>
