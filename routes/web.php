<?php

use Illuminate\Support\Facades\Route;
use LaravelApiBuilder\Controllers\EndpointController;

Route::get('/', [EndpointController::class, 'index'])->name('api-builder.index');
Route::post('/endpoints', [EndpointController::class, 'store'])->name('api-builder.store');
Route::put('/endpoints/{endpoint}', [EndpointController::class, 'update'])->name('api-builder.update');
Route::delete('/endpoints/{endpoint}', [EndpointController::class, 'destroy'])->name('api-builder.destroy');
