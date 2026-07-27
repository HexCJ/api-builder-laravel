<?php

namespace LaravelApiBuilder\Builders;

use Illuminate\Database\Query\Builder;
use LaravelApiBuilder\Contracts\AppliesQueryConfiguration;
use LaravelApiBuilder\Exceptions\InvalidConfigurationException;
use LaravelApiBuilder\Support\ColumnReference;

/**
 * Adds common SQL window function expressions through Query Builder raw selections.
 */
class WindowBuilder implements AppliesQueryConfiguration
{
    private const FUNCTIONS = ['row_number', 'rank', 'dense_rank'];

    public function apply(Builder $query, array $configuration): Builder
    {
        if (! config('api-builder.security.allow_raw_expressions', false) && ($configuration['window'] ?? []) !== []) {
            throw new InvalidConfigurationException('Window functions require raw expressions to be enabled.');
        }

        foreach ($configuration['window'] ?? [] as $window) {
            $function = strtolower((string) ($window['function'] ?? 'row_number'));
            $alias = ColumnReference::assertAlias((string) ($window['alias'] ?? $function));

            if (! in_array($function, self::FUNCTIONS, true)) {
                throw new InvalidConfigurationException("Unsupported window function [{$function}].");
            }

            $partition = collect($window['partition_by'] ?? [])
                ->map(fn ($column) => ColumnReference::assertValid((string) $column))
                ->implode(', ');
            $order = collect($window['order_by'] ?? [])
                ->map(function ($order): string {
                    $direction = strtolower((string) ($order['direction'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';

                    return ColumnReference::assertValid((string) $order['column']).' '.$direction;
                })
                ->implode(', ');

            $parts = [];
            if ($partition !== '') {
                $parts[] = "PARTITION BY {$partition}";
            }
            if ($order !== '') {
                $parts[] = "ORDER BY {$order}";
            }

            $query->selectRaw(strtoupper($function).'() OVER ('.implode(' ', $parts).") as {$alias}");
        }

        return $query;
    }
}
