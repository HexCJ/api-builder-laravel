<?php

namespace LaravelApiBuilder\Support;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use LaravelApiBuilder\Exceptions\InvalidConfigurationException;

/**
 * Centralizes opt-in raw database expressions for computed columns and advanced filters.
 */
final class ExpressionFactory
{
    public function make(string $expression): Expression
    {
        if (! config('api-builder.security.allow_raw_expressions', false)) {
            throw new InvalidConfigurationException('Raw expressions are disabled.');
        }

        return DB::raw($expression);
    }
}
