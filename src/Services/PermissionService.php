<?php

namespace LaravelApiBuilder\Services;

use Illuminate\Http\Request;
use LaravelApiBuilder\Exceptions\UnauthorizedEndpointException;
use LaravelApiBuilder\Models\ApiEndpoint;

/**
 * Verifies endpoint-level authentication requirements.
 */
class PermissionService
{
    public function assertAllowed(Request $request, ApiEndpoint $endpoint): void
    {
        if (! $endpoint->auth_required) {
            return;
        }

        if ($request->user() !== null) {
            return;
        }

        throw new UnauthorizedEndpointException('Authentication is required for this endpoint.');
    }
}
