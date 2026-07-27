<?php

namespace LaravelApiBuilder\Builders;

use Illuminate\Database\Query\Builder;
use LaravelApiBuilder\Contracts\AppliesQueryConfiguration;
use LaravelApiBuilder\Support\ColumnReference;

/**
 * Applies configured select columns.
 */
class SelectBuilder implements AppliesQueryConfiguration
{
    public function apply(Builder $query, array $configuration): Builder
    {
        $columns = $configuration['columns'] ?? [];

        if ($columns === [] || $columns === ['*']) {
            return $query->select('*');
        }

        return $query->select(array_map(
            fn (string $column): string => ColumnReference::assertValid($column),
            $columns
        ));
    }
}
