<?php

namespace LaravelApiBuilder\Services;

use LaravelApiBuilder\Exceptions\InvalidConfigurationException;
use LaravelApiBuilder\Support\ColumnReference;
use LaravelApiBuilder\Support\ConfigurationDefaults;

/**
 * Normalizes and validates endpoint configuration payloads.
 */
class ConfigurationService
{
    private const METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    private const WHERE_OPERATORS = ['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'NOT LIKE', 'BETWEEN', 'NOT BETWEEN', 'NULL', 'NOT NULL', 'IN', 'NOT IN', 'EXISTS', 'RAW'];

    public function __construct(private readonly MetadataService $metadata)
    {
    }

    /**
     * @param array<string, mixed> $configuration
     * @return array<string, mixed>
     */
    public function normalize(array $configuration): array
    {
        return array_replace_recursive(ConfigurationDefaults::values(), $configuration);
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function validate(string $table, string $method, array $configuration): void
    {
        if (! in_array(strtoupper($method), self::METHODS, true)) {
            throw new InvalidConfigurationException("HTTP method [{$method}] is not supported.");
        }

        $this->metadata->assertTableExists($table);
        $this->validateColumns($table, $configuration);
        $this->validateJoins($configuration);
        $this->validateWith($configuration);
        $this->validateWhere($configuration['where'] ?? []);
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function validateColumns(string $table, array $configuration): void
    {
        foreach (['columns', 'group_by'] as $key) {
            foreach ($configuration[$key] ?? [] as $column) {
                $this->validateColumnReference($table, (string) $column);
            }
        }

        foreach (['order_by', 'having'] as $key) {
            foreach ($configuration[$key] ?? [] as $item) {
                $this->validateColumnReference($table, (string) ($item['column'] ?? ''));
            }
        }

        foreach ($configuration['aliases'] ?? [] as $alias) {
            $this->validateColumnReference($table, (string) ($alias['column'] ?? ''));
            ColumnReference::assertAlias((string) ($alias['alias'] ?? ''));
        }
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function validateJoins(array $configuration): void
    {
        foreach ($configuration['joins'] ?? [] as $join) {
            $joinTable = (string) ($join['table'] ?? '');
            $this->metadata->assertTableExists($joinTable);
            if (($join['type'] ?? 'inner') !== 'cross') {
                $this->validateColumnReference('', (string) ($join['first'] ?? ''));
                $this->validateColumnReference('', (string) ($join['second'] ?? ''));
            }
        }
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function validateWith(array $configuration): void
    {
        foreach ($configuration['with'] ?? [] as $include) {
            $table = (string) ($include['table'] ?? '');
            $this->metadata->assertTableExists($table);
            $this->metadata->assertColumnExists($table, (string) ($include['column'] ?? 'id'));
            ColumnReference::assertAlias((string) ($include['alias'] ?? 'included_value'));

            foreach ($include['where_column'] ?? [] as $condition) {
                ColumnReference::assertValid((string) ($condition['first'] ?? ''));
                ColumnReference::assertValid((string) ($condition['second'] ?? ''));
                $this->assertSimpleOperator((string) ($condition['operator'] ?? '='));
            }

            foreach ($include['where'] ?? [] as $condition) {
                ColumnReference::assertValid((string) ($condition['column'] ?? ''));
                $this->assertSimpleOperator((string) ($condition['operator'] ?? '='));
            }
        }
    }

    /**
     * @param array<int, array<string, mixed>> $conditions
     */
    private function validateWhere(array $conditions): void
    {
        foreach ($conditions as $condition) {
            if (isset($condition['nested'])) {
                $this->validateWhere((array) $condition['nested']);
                continue;
            }

            $operator = strtoupper((string) ($condition['operator'] ?? '='));
            if (! in_array($operator, self::WHERE_OPERATORS, true)) {
                throw new InvalidConfigurationException("Invalid where operator [{$operator}].");
            }

            if ($operator === 'EXISTS') {
                $this->metadata->assertTableExists((string) ($condition['table'] ?? ''));
                $this->validateWhere((array) ($condition['where'] ?? []));
                continue;
            }

            if (! in_array($operator, ['RAW', 'EXISTS'], true)) {
                ColumnReference::assertValid((string) ($condition['column'] ?? ''));
            }
        }
    }

    private function validateColumnReference(string $baseTable, string $reference): void
    {
        ColumnReference::assertValid($reference);

        if ($baseTable !== '' && ! str_contains($reference, '.')) {
            $this->metadata->assertColumnExists($baseTable, $reference);
        }
    }

    private function assertSimpleOperator(string $operator): void
    {
        if (! in_array($operator, ['=', '!=', '<', '>', '<=', '>='], true)) {
            throw new InvalidConfigurationException("Invalid operator [{$operator}].");
        }
    }
}
