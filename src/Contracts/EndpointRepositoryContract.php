<?php

namespace LaravelApiBuilder\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use LaravelApiBuilder\DTO\EndpointData;
use LaravelApiBuilder\Models\ApiEndpoint;

interface EndpointRepositoryContract
{
    public function findByPathAndMethod(string $path, string $method): ?ApiEndpoint;

    public function create(EndpointData $data): ApiEndpoint;

    public function update(ApiEndpoint $endpoint, EndpointData $data): ApiEndpoint;

    public function delete(ApiEndpoint $endpoint): void;

    public function paginate(int $perPage = 25): LengthAwarePaginator;

    /**
     * @return Collection<int, ApiEndpoint>
     */
    public function active(): Collection;
}
