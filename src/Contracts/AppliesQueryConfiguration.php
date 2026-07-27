<?php

namespace LaravelApiBuilder\Contracts;

use Illuminate\Database\Query\Builder;

/**
 * Applies one isolated endpoint configuration feature to a query builder.
 */
interface AppliesQueryConfiguration
{
    /**
     * @param array<string, mixed> $configuration
     */
    public function apply(Builder $query, array $configuration): Builder;
}
