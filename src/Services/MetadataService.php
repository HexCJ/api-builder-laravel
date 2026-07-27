<?php

namespace LaravelApiBuilder\Services;

use Illuminate\Support\Facades\Schema;
use LaravelApiBuilder\Contracts\MetadataServiceContract;
use LaravelApiBuilder\Exceptions\InvalidConfigurationException;

/**
 * Reads database schema metadata for the administrator UI and validators.
 */
class MetadataService implements MetadataServiceContract
{
    public function tables(): array
    {
        $blocked = config('api-builder.metadata.blocked_tables', []);
        $tables = Schema::getTables();

        return collect($tables)
            ->map(fn (array $table): string => $table['name'])
            ->reject(fn (string $table): bool => in_array($table, $blocked, true))
            ->values()
            ->all();
    }

    public function table(string $table): array
    {
        $this->assertTableExists($table);

        return [
            'name' => $table,
            'columns' => Schema::getColumns($table),
            'primary_key' => $this->primaryKey($table),
            'foreign_keys' => method_exists(Schema::getFacadeRoot(), 'getForeignKeys') ? Schema::getForeignKeys($table) : [],
            'indexes' => method_exists(Schema::getFacadeRoot(), 'getIndexes') ? Schema::getIndexes($table) : [],
        ];
    }

    public function assertTableExists(string $table): void
    {
        if (! Schema::hasTable($table)) {
            throw new InvalidConfigurationException("Table [{$table}] does not exist.");
        }
    }

    public function assertColumnExists(string $table, string $column): void
    {
        $column = str_contains($column, '.') ? explode('.', $column)[1] : $column;

        if ($column !== '*' && ! Schema::hasColumn($table, $column)) {
            throw new InvalidConfigurationException("Column [{$table}.{$column}] does not exist.");
        }
    }

    private function primaryKey(string $table): ?string
    {
        $columns = Schema::getColumns($table);

        foreach ($columns as $column) {
            if (($column['primary'] ?? false) === true) {
                return $column['name'];
            }
        }

        return null;
    }
}
