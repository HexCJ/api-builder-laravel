<?php

namespace LaravelApiBuilder\DTO;

/**
 * Immutable data transfer object for endpoint persistence.
 */
final readonly class EndpointData
{
    /**
     * @param array<string, mixed> $configuration
     */
    public function __construct(
        public string $name,
        public string $path,
        public string $method,
        public string $tableName,
        public ?string $description,
        public bool $authRequired,
        public bool $active,
        public array $configuration,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            name: (string) $payload['name'],
            path: trim((string) $payload['path'], '/'),
            method: strtoupper((string) $payload['method']),
            tableName: (string) $payload['table_name'],
            description: $payload['description'] ?? null,
            authRequired: (bool) ($payload['auth_required'] ?? false),
            active: (bool) ($payload['active'] ?? true),
            configuration: $payload['configuration'] ?? [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'path' => $this->path,
            'method' => $this->method,
            'table_name' => $this->tableName,
            'description' => $this->description,
            'auth_required' => $this->authRequired,
            'active' => $this->active,
            'configuration' => $this->configuration,
        ];
    }
}
