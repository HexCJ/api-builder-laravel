<?php

namespace LaravelApiBuilder\Builders;

use Illuminate\Database\Query\Builder;
use LaravelApiBuilder\Contracts\AppliesQueryConfiguration;
use LaravelApiBuilder\Support\ColumnReference;

/**
 * Adds aliased column selections.
 */
class AliasBuilder implements AppliesQueryConfiguration
{
    public function apply(Builder $query, array $configuration): Builder
    {
        foreach ($configuration['aliases'] ?? [] as $alias) {
            $column = ColumnReference::assertValid((string) ($alias['column'] ?? ''));
            $name = ColumnReference::assertAlias((string) ($alias['alias'] ?? ''));
            $query->addSelect("{$column} as {$name}");
        }

        return $query;
    }
}
