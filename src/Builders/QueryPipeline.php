<?php

namespace LaravelApiBuilder\Builders;

use Illuminate\Database\Query\Builder;

/**
 * Runs endpoint query builders in a deterministic order.
 */
class QueryPipeline
{
    /**
     * @param array<int, object> $builders
     */
    public function __construct(private readonly array $builders)
    {
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function apply(Builder $query, array $configuration): Builder
    {
        foreach ($this->builders as $builder) {
            $query = $builder->apply($query, $configuration);
        }

        return $query;
    }
}
