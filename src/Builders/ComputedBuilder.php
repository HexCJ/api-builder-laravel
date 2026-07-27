<?php

namespace LaravelApiBuilder\Builders;

use Illuminate\Database\Query\Builder;
use LaravelApiBuilder\Contracts\AppliesQueryConfiguration;
use LaravelApiBuilder\Support\ColumnReference;
use LaravelApiBuilder\Support\ExpressionFactory;

/**
 * Adds opt-in computed select expressions.
 */
class ComputedBuilder implements AppliesQueryConfiguration
{
    public function __construct(private readonly ExpressionFactory $expressions)
    {
    }

    public function apply(Builder $query, array $configuration): Builder
    {
        foreach ($configuration['computed'] ?? [] as $computed) {
            $alias = ColumnReference::assertAlias((string) ($computed['alias'] ?? 'computed'));
            $query->addSelect($this->expressions->make((string) $computed['expression'].' as '.$alias));
        }

        return $query;
    }
}
