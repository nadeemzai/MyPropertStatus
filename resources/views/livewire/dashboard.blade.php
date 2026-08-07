<div>
    <h1 class="text-2xl font-semibold text-gray-900">Welcome, {{ auth()->user()->name }}</h1>
    <p class="mt-2 text-gray-600">
        Manage your properties from
        <a href="{{ route('dashboard.properties.index') }}" class="font-medium text-green-700 hover:text-green-800">My Properties</a>.
    </p>

    @unless (auth()->user()->avatar)
        <div class="mt-6 flex items-center justify-between rounded-lg border border-green-200 bg-green-50 p-4">
            <div>
                <p class="font-medium text-green-900">Complete your profile</p>
                <p class="text-sm text-green-700">Add a profile photo so agencies and owners recognize you.</p>
            </div>
            <a href="{{ route('dashboard.settings') }}" class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">
                Complete profile
            </a>
        </div>
    @endunless
</div>
