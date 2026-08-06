<div class="space-y-16">
    <section class="rounded-2xl bg-gray-900 px-8 py-16 text-center text-white">
        <h1 class="text-3xl font-bold sm:text-4xl">Find your next property</h1>
        <p class="mt-3 text-gray-300">Browse verified listings from agencies across the country.</p>

        <form method="GET" action="{{ route('properties.index') }}" class="mx-auto mt-8 flex max-w-xl flex-col gap-3 sm:flex-row">
            <input
                type="text"
                name="location"
                placeholder="City or area"
                class="w-full rounded-md border-0 bg-white px-4 py-2 text-gray-900 shadow-sm focus:ring-2 focus:ring-white sm:flex-1"
            >

            <select name="type" class="w-full rounded-md border-0 bg-white px-4 py-2 text-gray-900 shadow-sm focus:ring-2 focus:ring-white sm:w-40">
                <option value="">Any type</option>
                <option value="apartment">Apartment</option>
                <option value="house">House</option>
                <option value="land">Land</option>
                <option value="commercial">Commercial</option>
            </select>

            <button type="submit" class="rounded-md bg-white px-6 py-2 font-semibold text-gray-900 hover:bg-gray-100">
                Search
            </button>
        </form>
    </section>

    <section>
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">Recently listed</h2>
            <a href="{{ route('properties.index') }}" class="text-sm text-gray-600 hover:text-gray-900">View all &rarr;</a>
        </div>

        @if ($recentProperties->isEmpty())
            <p class="text-gray-500">No properties are published yet. Check back soon.</p>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($recentProperties as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
        @endif
    </section>
</div>
