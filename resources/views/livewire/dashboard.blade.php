<div>
    <h1 class="text-2xl font-semibold text-gray-900">Welcome, {{ auth()->user()->name }}</h1>
    <p class="mt-2 text-gray-600">
        Manage your properties from
        <a href="{{ route('dashboard.properties.index') }}" class="font-medium text-green-700 hover:text-green-800">My Properties</a>.
    </p>
</div>
