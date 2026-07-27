<?php

namespace LaravelApiBuilder\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use LaravelApiBuilder\DTO\EndpointData;
use LaravelApiBuilder\Http\Requests\StoreEndpointRequest;
use LaravelApiBuilder\Http\Requests\UpdateEndpointRequest;
use LaravelApiBuilder\Models\ApiEndpoint;
use LaravelApiBuilder\Services\EndpointService;
use LaravelApiBuilder\Services\MetadataService;

/**
 * Provides CRUD pages for administrator-managed endpoint definitions.
 */
class EndpointController extends Controller
{
    public function __construct(
        private readonly EndpointService $service,
        private readonly MetadataService $metadata,
    ) {
    }

    public function index(Request $request): View
    {
        $editingEndpoint = $request->filled('edit')
            ? ApiEndpoint::query()->find($request->integer('edit'))
            : null;

        return view('api-builder::index', [
            'endpoints' => ApiEndpoint::query()->latest()->paginate(20),
            'tables' => $this->metadata->tables(),
            'editingEndpoint' => $editingEndpoint,
            'editingEndpointPayload' => $editingEndpoint ? [
                'id' => $editingEndpoint->id,
                'table_name' => $editingEndpoint->table_name,
                'configuration' => $editingEndpoint->configuration ?? [],
            ] : null,
            'initialConfiguration' => $editingEndpoint?->configuration ?? [],
        ]);
    }

    public function store(StoreEndpointRequest $request): RedirectResponse
    {
        $this->service->store(EndpointData::fromArray($this->payload($request)));

        return back()->with('status', 'Endpoint created.');
    }

    public function update(UpdateEndpointRequest $request, ApiEndpoint $endpoint): RedirectResponse
    {
        $this->service->update($endpoint, EndpointData::fromArray($this->payload($request)));

        return back()->with('status', 'Endpoint updated.');
    }

    public function destroy(ApiEndpoint $endpoint): RedirectResponse
    {
        $endpoint->delete();

        return back()->with('status', 'Endpoint deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request): array
    {
        $payload = $request->validated();
        $payload['auth_required'] = $request->boolean('auth_required');
        $payload['active'] = $request->has('active') ? $request->boolean('active') : true;
        $payload['configuration'] = $request->input('configuration', []);

        if (is_string($payload['configuration'])) {
            $payload['configuration'] = json_decode($payload['configuration'], true, flags: JSON_THROW_ON_ERROR);
        }

        return $payload;
    }
}
