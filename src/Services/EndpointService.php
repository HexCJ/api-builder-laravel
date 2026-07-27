<?php

namespace LaravelApiBuilder\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LaravelApiBuilder\Builders\PaginationBuilder;
use LaravelApiBuilder\Builders\QueryPipeline;
use LaravelApiBuilder\Contracts\EndpointRepositoryContract;
use LaravelApiBuilder\DTO\EndpointData;
use LaravelApiBuilder\Exceptions\EndpointNotFoundException;
use LaravelApiBuilder\Exceptions\InvalidConfigurationException;
use LaravelApiBuilder\Models\ApiEndpoint;

/**
 * Coordinates endpoint persistence and runtime execution.
 */
class EndpointService
{
    public function __construct(
        private readonly EndpointRepositoryContract $endpoints,
        private readonly ConfigurationService $configuration,
        private readonly PermissionService $permissions,
        private readonly QueryPipeline $pipeline,
        private readonly PaginationBuilder $pagination,
    ) {
    }

    public function store(EndpointData $data): ApiEndpoint
    {
        $data = $this->withNormalizedConfiguration($data);
        $this->configuration->validate($data->tableName, $data->method, $data->configuration);

        return $this->endpoints->create($data);
    }

    public function update(ApiEndpoint $endpoint, EndpointData $data): ApiEndpoint
    {
        $data = $this->withNormalizedConfiguration($data);
        $this->configuration->validate($data->tableName, $data->method, $data->configuration);

        return $this->endpoints->update($endpoint, $data);
    }

    public function execute(Request $request, string $path): mixed
    {
        $endpoint = $this->endpoints->findByPathAndMethod($path, $request->method());

        if (! $endpoint) {
            throw new EndpointNotFoundException('Endpoint not found.');
        }

        if (! $endpoint->active) {
            throw new InvalidConfigurationException('Endpoint is inactive.');
        }

        $this->permissions->assertAllowed($request, $endpoint);
        $configuration = $this->configuration->normalize($endpoint->configuration ?? []);
        $this->configuration->validate($endpoint->table_name, $endpoint->method, $configuration);

        $query = DB::table($endpoint->table_name);
        $query = $this->pipeline->apply($query, $configuration);

        return $this->pagination->execute($query, $configuration);
    }

    private function withNormalizedConfiguration(EndpointData $data): EndpointData
    {
        return new EndpointData(
            $data->name,
            $data->path,
            $data->method,
            $data->tableName,
            $data->description,
            $data->authRequired,
            $data->active,
            $this->configuration->normalize($data->configuration),
        );
    }
}
