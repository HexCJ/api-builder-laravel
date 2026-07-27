<?php

namespace LaravelApiBuilder\Builders;

use Illuminate\Database\Query\Builder;
use LaravelApiBuilder\Contracts\AppliesQueryConfiguration;
use LaravelApiBuilder\Exceptions\InvalidConfigurationException;
use LaravelApiBuilder\Support\ColumnReference;

/**
 * Applies configured INNER, LEFT, RIGHT, and CROSS joins.
 */
class JoinBuilder implements AppliesQueryConfiguration
{
    private const TYPES = ['inner', 'left', 'right', 'cross'];

    public function apply(Builder $query, array $configuration): Builder
    {
        foreach ($configuration['joins'] ?? [] as $join) {
            $type = strtolower((string) ($join['type'] ?? 'inner'));
            $table = ColumnReference::assertValid((string) ($join['table'] ?? ''));

            if (! in_array($type, self::TYPES, true)) {
                throw new InvalidConfigurationException("Unsupported join type [{$type}].");
            }

            if ($type === 'cross') {
                $query->crossJoin($table);
                continue;
            }

            $first = ColumnReference::assertValid((string) ($join['first'] ?? ''));
            $operator = (string) ($join['operator'] ?? '=');
            $second = ColumnReference::assertValid((string) ($join['second'] ?? ''));

            if (! in_array($operator, ['=', '!=', '<', '>', '<=', '>='], true)) {
                throw new InvalidConfigurationException("Unsupported join operator [{$operator}].");
            }

            $method = $type === 'inner' ? 'join' : $type.'Join';
            $query->{$method}($table, $first, $operator, $second);
        }

        return $query;
    }
}
