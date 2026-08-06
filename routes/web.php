<?php

use App\Livewire\Dashboard;
use App\Livewire\Dashboard\MyProperties;
use App\Livewire\Dashboard\PropertyForm;
use App\Livewire\Public\Browse;
use App\Livewire\Public\Home;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/properties', Browse::class)->name('properties.index');

Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/properties', MyProperties::class)->name('properties.index');
    Route::get('/properties/create', PropertyForm::class)->name('properties.create');
    Route::get('/properties/{id}/edit', PropertyForm::class)->name('properties.edit');
});

Route::get('/dashboard', Dashboard::class)
    ->middleware('auth')
    ->name('dashboard');

require __DIR__.'/auth.php';
