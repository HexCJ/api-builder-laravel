<?php

namespace LaravelApiBuilder\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use LaravelApiBuilder\Services\MetadataService;

/**
 * Exposes schema metadata to the administrator UI.
 */
class MetadataController extends Controller
{
    public function __construct(private readonly MetadataService $metadata)
    {
    }

    public function tables(): JsonResponse
    {
        return response()->json($this->metadata->tables());
    }

    public function table(string $table): JsonResponse
    {
        return response()->json($this->metadata->table($table));
    }
}
