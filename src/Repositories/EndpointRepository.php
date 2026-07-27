<?php

namespace LaravelApiBuilder\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use LaravelApiBuilder\Contracts\EndpointRepositoryContract;
use LaravelApiBuilder\DTO\EndpointData;
use LaravelApiBuilder\Models\ApiEndpoint;

/**
 * Eloquent-backed repository for endpoint definitions.
 */
class EndpointRepository implements EndpointRepositoryContract
{
    public function findByPathAndMethod(string $path, string $method): ?ApiEndpoint
    {
        return ApiEndpoint::query()
            ->where('path', trim($path, '/'))
            ->where('method', strtoupper($method))
            ->first();
    }

    public function create(EndpointData $data): ApiEndpoint
    {
        return ApiEndpoint::query()->create($data->toArray());
    }

    public function update(ApiEndpoint $endpoint, EndpointData $data): ApiEndpoint
    {
        $endpoint->fill($data->toArray())->save();

        return $endpoint->refresh();
    }

    public function delete(ApiEndpoint $endpoint): void
    {
        $endpoint->delete();
    }

    public function paginate(int $perPage = 25): LengthAwarePaginator
    {
        return ApiEndpoint::query()->latest()->paginate($perPage);
    }

    public function active(): Collection
    {
        return ApiEndpoint::query()->active()->get();
    }
}
