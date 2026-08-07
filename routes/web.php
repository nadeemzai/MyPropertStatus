<?php

use App\Livewire\Dashboard;
use App\Livewire\Dashboard\MyConnections;
use App\Livewire\Dashboard\MyListings;
use App\Livewire\Dashboard\MyProperties;
use App\Livewire\Dashboard\Notifications;
use App\Livewire\Dashboard\PropertyForm;
use App\Livewire\Dashboard\ProfileSettings;
use App\Livewire\Dashboard\SupportTickets;
use App\Livewire\Public\Browse;
use App\Livewire\Public\Home;
use App\Livewire\Public\Show;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/properties', Browse::class)->name('properties.index');
Route::get('/properties/{id}', Show::class)->name('properties.show');

Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/properties', MyProperties::class)->name('properties.index');
    Route::get('/properties/create', PropertyForm::class)->name('properties.create');
    Route::get('/properties/{id}/edit', PropertyForm::class)->name('properties.edit');
    Route::get('/listings', MyListings::class)->name('listings.index');
    Route::get('/connections', MyConnections::class)->name('connections.index');
    Route::get('/settings', ProfileSettings::class)->name('settings');
    Route::get('/support', SupportTickets::class)->name('support.index');
    Route::get('/notifications', Notifications::class)->name('notifications.index');
});

Route::get('/dashboard', Dashboard::class)
    ->middleware('auth')
    ->name('dashboard');

require __DIR__.'/auth.php';
