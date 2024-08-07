<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HashController;

Route::get('/hash/{hash}', [HashController::class, 'read']);
Route::post('/hash', [HashController::class, 'store']);
