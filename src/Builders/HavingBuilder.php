<?php

namespace LaravelApiBuilder\Builders;

use Illuminate\Database\Query\Builder;
use LaravelApiBuilder\Contracts\AppliesQueryConfiguration;
use LaravelApiBuilder\Exceptions\InvalidConfigurationException;
use LaravelApiBuilder\Support\ColumnReference;

/**
 * Applies configured HAVING filters for aggregate queries.
 */
class HavingBuilder implements AppliesQueryConfiguration
{
    private const OPERATORS = ['=', '!=', '<', '>', '<=', '>='];

    public function apply(Builder $query, array $configuration): Builder
    {
        foreach ($configuration['having'] ?? [] as $having) {
            $column = ColumnReference::assertValid((string) ($having['column'] ?? ''));
            $operator = (string) ($having['operator'] ?? '=');
            $value = $having['value'] ?? null;
            $boolean = strtolower((string) ($having['boolean'] ?? 'and')) === 'or' ? 'or' : 'and';

            if (! in_array($operator, self::OPERATORS, true)) {
                throw new InvalidConfigurationException("Unsupported having operator [{$operator}].");
            }

            $query->having($column, $operator, $value, $boolean);
        }

        return $query;
    }
}
