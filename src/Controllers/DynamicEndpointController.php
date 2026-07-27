<?php

namespace LaravelApiBuilder\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelApiBuilder\Exceptions\ApiBuilderException;
use LaravelApiBuilder\Exceptions\EndpointNotFoundException;
use LaravelApiBuilder\Exceptions\UnauthorizedEndpointException;
use LaravelApiBuilder\Services\EndpointService;

/**
 * Handles all runtime dynamic API requests through one route.
 */
class DynamicEndpointController extends Controller
{
    public function __construct(private readonly EndpointService $endpoints)
    {
    }

    public function __invoke(Request $request, string $dynamicEndpoint): JsonResponse
    {
        try {
            return response()->json([
                'data' => $this->endpoints->execute($request, $dynamicEndpoint),
            ]);
        } catch (EndpointNotFoundException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        } catch (UnauthorizedEndpointException $exception) {
            return response()->json(['message' => $exception->getMessage()], 401);
        } catch (ApiBuilderException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }
    }
}
