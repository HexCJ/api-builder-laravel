<?php

namespace LaravelApiBuilder\Builders;

use Illuminate\Database\Query\Builder;
use LaravelApiBuilder\Contracts\AppliesQueryConfiguration;
use LaravelApiBuilder\Exceptions\InvalidConfigurationException;
use LaravelApiBuilder\Support\ColumnReference;
use LaravelApiBuilder\Support\ExpressionFactory;

/**
 * Applies scalar, nested, existence, set, range, null, and opt-in raw where clauses.
 */
class WhereBuilder implements AppliesQueryConfiguration
{
    private const BASIC = ['=', '!=', '<', '>', '<=', '>=', 'like', 'not like'];

    public function __construct(private readonly ExpressionFactory $expressions)
    {
    }

    public function apply(Builder $query, array $configuration): Builder
    {
        foreach ($configuration['where'] ?? [] as $condition) {
            $this->applyCondition($query, $condition);
        }

        return $query;
    }

    /**
     * @param array<string, mixed> $condition
     */
    private function applyCondition(Builder $query, array $condition, string $boolean = 'and'): void
    {
        $operator = strtoupper((string) ($condition['operator'] ?? '='));
        $boolean = strtolower((string) ($condition['boolean'] ?? $boolean)) === 'or' ? 'or' : 'and';

        if (isset($condition['nested'])) {
            $method = $boolean === 'or' ? 'orWhere' : 'where';
            $query->{$method}(function (Builder $nested) use ($condition): void {
                foreach ($condition['nested'] as $child) {
                    $this->applyCondition($nested, $child);
                }
            });
            return;
        }

        if ($operator === 'RAW') {
            $query->where($this->expressions->make((string) $condition['expression']), null, null, $boolean);
            return;
        }

        if ($operator === 'EXISTS') {
            $table = ColumnReference::assertValid((string) ($condition['table'] ?? ''));
            $query->{$boolean === 'or' ? 'orWhereExists' : 'whereExists'}(function (Builder $exists) use ($condition, $table): void {
                $exists->from($table);
                foreach ($condition['where'] ?? [] as $child) {
                    $this->applyCondition($exists, $child);
                }
            });
            return;
        }

        $column = ColumnReference::assertValid((string) ($condition['column'] ?? ''));
        $value = $condition['value'] ?? null;

        match ($operator) {
            'NULL' => $query->{$boolean === 'or' ? 'orWhereNull' : 'whereNull'}($column),
            'NOT NULL' => $query->{$boolean === 'or' ? 'orWhereNotNull' : 'whereNotNull'}($column),
            'BETWEEN' => $query->{$boolean === 'or' ? 'orWhereBetween' : 'whereBetween'}($column, (array) $value),
            'NOT BETWEEN' => $query->{$boolean === 'or' ? 'orWhereNotBetween' : 'whereNotBetween'}($column, (array) $value),
            'IN' => $query->{$boolean === 'or' ? 'orWhereIn' : 'whereIn'}($column, (array) $value),
            'NOT IN' => $query->{$boolean === 'or' ? 'orWhereNotIn' : 'whereNotIn'}($column, (array) $value),
            default => $this->applyBasic($query, $column, strtolower($operator), $value, $boolean),
        };
    }

    private function applyBasic(Builder $query, string $column, string $operator, mixed $value, string $boolean): void
    {
        if (! in_array($operator, self::BASIC, true)) {
            throw new InvalidConfigurationException("Unsupported where operator [{$operator}].");
        }

        $query->where($column, $operator, $value, $boolean);
    }
}
