<?php

use Illuminate\Support\Facades\Route;
use LaravelApiBuilder\Controllers\DynamicEndpointController;
use LaravelApiBuilder\Controllers\MetadataController;
use LaravelApiBuilder\Controllers\SwaggerController;

Route::prefix('builder')->group(function (): void {
    Route::get('/tables', [MetadataController::class, 'tables'])->name('api-builder.tables');
    Route::get('/table/{table}', [MetadataController::class, 'table'])->name('api-builder.table');
    Route::get('/swagger.json', SwaggerController::class)->name('api-builder.swagger');
});

Route::any('/{dynamicEndpoint}', DynamicEndpointController::class)
    ->where('dynamicEndpoint', '.*')
    ->name('api-builder.dynamic');
