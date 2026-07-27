<?php

namespace LaravelApiBuilder\Builders;

use Illuminate\Database\Query\Builder;
use LaravelApiBuilder\Contracts\AppliesQueryConfiguration;
use LaravelApiBuilder\Support\ColumnReference;

/**
 * Applies one or more ORDER BY clauses.
 */
class OrderBuilder implements AppliesQueryConfiguration
{
    public function apply(Builder $query, array $configuration): Builder
    {
        foreach ($configuration['order_by'] ?? [] as $order) {
            $direction = strtolower((string) ($order['direction'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';
            $query->orderBy(ColumnReference::assertValid((string) ($order['column'] ?? '')), $direction);
        }

        return $query;
    }
}
