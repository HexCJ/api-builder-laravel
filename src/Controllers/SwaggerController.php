<?php

namespace LaravelApiBuilder\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use LaravelApiBuilder\Services\SwaggerService;

/**
 * Returns generated OpenAPI documentation.
 */
class SwaggerController extends Controller
{
    public function __construct(private readonly SwaggerService $swagger)
    {
    }

    public function __invoke(): JsonResponse
    {
        return response()->json($this->swagger->document());
    }
}
