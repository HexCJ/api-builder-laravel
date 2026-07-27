<?php

namespace LaravelApiBuilder\Support;

use LaravelApiBuilder\Exceptions\InvalidConfigurationException;

/**
 * Validates identifier-like column and table references before they reach Query Builder.
 */
final class ColumnReference
{
    public static function assertValid(string $reference): string
    {
        if (! preg_match('/^[A-Za-z_][A-Za-z0-9_]*(\.[A-Za-z_][A-Za-z0-9_]*)?$/', $reference)) {
            throw new InvalidConfigurationException("Invalid column reference [{$reference}].");
        }

        return $reference;
    }

    public static function assertAlias(string $alias): string
    {
        if (! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $alias)) {
            throw new InvalidConfigurationException("Invalid alias [{$alias}].");
        }

        return $alias;
    }
}
