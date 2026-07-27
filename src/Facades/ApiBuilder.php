<?php

namespace LaravelApiBuilder\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the endpoint service.
 *
 * @method static mixed execute(\Illuminate\Http\Request $request, string $path)
 */
class ApiBuilder extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'api-builder';
    }
}
