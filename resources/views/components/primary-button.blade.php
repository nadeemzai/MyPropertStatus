<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex w-full items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
