<?php

namespace LaravelApiBuilder\Builders;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use LaravelApiBuilder\Contracts\AppliesQueryConfiguration;
use LaravelApiBuilder\Exceptions\InvalidConfigurationException;
use LaravelApiBuilder\Support\ColumnReference;

/**
 * Applies query-builder subselect includes configured through the "with" key.
 */
class WithBuilder implements AppliesQueryConfiguration
{
    private const OPERATORS = ['=', '!=', '<', '>', '<=', '>='];

    public function apply(Builder $query, array $configuration): Builder
    {
        foreach ($configuration['with'] ?? [] as $include) {
            $alias = ColumnReference::assertAlias((string) ($include['alias'] ?? 'included_value'));
            $table = ColumnReference::assertValid((string) ($include['table'] ?? ''));
            $column = ColumnReference::assertValid((string) ($include['column'] ?? 'id'));

            $subquery = DB::table($table)->select($column);

            foreach ($include['where_column'] ?? [] as $condition) {
                $first = ColumnReference::assertValid((string) ($condition['first'] ?? ''));
                $operator = (string) ($condition['operator'] ?? '=');
                $second = ColumnReference::assertValid((string) ($condition['second'] ?? ''));
                $this->assertOperator($operator);
                $subquery->whereColumn($first, $operator, $second);
            }

            foreach ($include['where'] ?? [] as $condition) {
                $operator = (string) ($condition['operator'] ?? '=');
                $this->assertOperator($operator);
                $subquery->where(
                    ColumnReference::assertValid((string) ($condition['column'] ?? '')),
                    $operator,
                    $condition['value'] ?? null
                );
            }

            foreach ($include['order_by'] ?? [] as $order) {
                $direction = strtolower((string) ($order['direction'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';
                $subquery->orderBy(ColumnReference::assertValid((string) ($order['column'] ?? '')), $direction);
            }

            if (($include['limit'] ?? null) !== null) {
                $subquery->limit((int) $include['limit']);
            }

            $query->selectSub($subquery, $alias);
        }

        return $query;
    }

    private function assertOperator(string $operator): void
    {
        if (! in_array($operator, self::OPERATORS, true)) {
            throw new InvalidConfigurationException("Unsupported with operator [{$operator}].");
        }
    }
}
