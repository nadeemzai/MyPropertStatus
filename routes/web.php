<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['MyPropertyStatus' => app()->version()];
});

require __DIR__.'/auth.php';
