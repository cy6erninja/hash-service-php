<?php

use Illuminate\Support\Facades\Route;

Route::get('/hash/{hash}', [HashController::class, 'show']);
Route::post('/hash', [HashController::class, 'store']);
