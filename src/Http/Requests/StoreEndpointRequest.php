<?php

namespace LaravelApiBuilder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates endpoint create requests from the administrator UI or JSON API.
 */
class StoreEndpointRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('configuration'))) {
            $decoded = json_decode($this->input('configuration'), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge(['configuration' => $decoded]);
            }
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique(config('api-builder.table', 'api_endpoints'), 'name')],
            'path' => ['required', 'string', 'max:255', Rule::unique(config('api-builder.table', 'api_endpoints'), 'path')],
            'method' => ['required', Rule::in(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])],
            'table_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'auth_required' => ['sometimes', 'boolean'],
            'active' => ['sometimes', 'boolean'],
            'configuration' => ['sometimes', 'array'],
        ];
    }
}
