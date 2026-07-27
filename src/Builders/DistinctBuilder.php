<?php

namespace LaravelApiBuilder\Builders;

use Illuminate\Database\Query\Builder;
use LaravelApiBuilder\Contracts\AppliesQueryConfiguration;

/**
 * Applies SELECT DISTINCT when requested.
 */
class DistinctBuilder implements AppliesQueryConfiguration
{
    public function apply(Builder $query, array $configuration): Builder
    {
        return ($configuration['distinct'] ?? false) ? $query->distinct() : $query;
    }
}
