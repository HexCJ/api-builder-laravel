<?php

namespace LaravelApiBuilder\Builders;

use Illuminate\Database\Query\Builder;
use LaravelApiBuilder\Contracts\AppliesQueryConfiguration;
use LaravelApiBuilder\Exceptions\InvalidConfigurationException;
use LaravelApiBuilder\Support\ColumnReference;

/**
 * Applies aggregate select expressions and GROUP BY columns.
 */
class AggregationBuilder implements AppliesQueryConfiguration
{
    private const FUNCTIONS = ['count', 'sum', 'avg', 'min', 'max'];

    public function apply(Builder $query, array $configuration): Builder
    {
        foreach ($configuration['aggregations'] ?? [] as $aggregate) {
            $function = strtolower((string) ($aggregate['function'] ?? ''));
            $column = (string) ($aggregate['column'] ?? '*');
            $column = $column === '*' ? '*' : ColumnReference::assertValid($column);
            $defaultAlias = $column === '*' ? "{$function}_all" : "{$function}_".str_replace('.', '_', $column);
            $alias = ColumnReference::assertAlias((string) ($aggregate['alias'] ?? $defaultAlias));

            if (! in_array($function, self::FUNCTIONS, true)) {
                throw new InvalidConfigurationException("Unsupported aggregation [{$function}].");
            }

            $query->selectRaw(strtoupper($function)."({$column}) as {$alias}");
        }

        foreach ($configuration['group_by'] ?? [] as $column) {
            $query->groupBy(ColumnReference::assertValid((string) $column));
        }

        return $query;
    }
}
