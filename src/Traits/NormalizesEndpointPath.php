<?php

namespace LaravelApiBuilder\Traits;

trait NormalizesEndpointPath
{
    protected function normalizeEndpointPath(string $path): string
    {
        return trim($path, '/');
    }
}
