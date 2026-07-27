<?php

namespace LaravelApiBuilder\Middleware;

use Closure;
use Illuminate\Http\Request;
use LaravelApiBuilder\Contracts\EndpointRepositoryContract;
use Symfony\Component\HttpFoundation\Response;

/**
 * Optional middleware for early rejection of inactive dynamic endpoints.
 */
class EnsureEndpointIsActive
{
    public function __construct(private readonly EndpointRepositoryContract $endpoints)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->route('dynamicEndpoint');

        if (! is_string($path)) {
            return $next($request);
        }

        $endpoint = $this->endpoints->findByPathAndMethod($path, $request->method());

        if ($endpoint && ! $endpoint->active) {
            return response()->json(['message' => 'Endpoint is inactive.'], 422);
        }

        return $next($request);
    }
}
