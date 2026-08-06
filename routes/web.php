<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['MyPropertyStatus' => app()->version()];
});

Route::get('/dashboard', Dashboard::class)
    ->middleware('auth')
    ->name('dashboard');

require __DIR__.'/auth.php';
