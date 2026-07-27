<?php

namespace LaravelApiBuilder\Support;

/**
 * Provides the canonical endpoint configuration shape.
 */
final class ConfigurationDefaults
{
    /**
     * @return array<string, mixed>
     */
    public static function values(): array
    {
        return [
            'columns' => [],
            'joins' => [],
            'where' => [],
            'with' => [],
            'aggregations' => [],
            'group_by' => [],
            'having' => [],
            'order_by' => [],
            'computed' => [],
            'aliases' => [],
            'window' => [],
            'pagination' => [],
            'distinct' => false,
            'limit' => null,
            'offset' => null,
        ];
    }
}
