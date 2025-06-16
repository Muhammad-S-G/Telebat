<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/api/documentation');
})->middleware(['web', 'swagger.no_csrf']);
