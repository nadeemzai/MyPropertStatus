<?php

use App\Livewire\Dashboard;
use App\Livewire\Public\Browse;
use App\Livewire\Public\Home;
use App\Livewire\Public\Show;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/properties', Browse::class)->name('properties.index');
Route::get('/properties/{id}', Show::class)->name('properties.show');

Route::get('/dashboard', Dashboard::class)
    ->middleware('auth')
    ->name('dashboard');

require __DIR__.'/auth.php';
