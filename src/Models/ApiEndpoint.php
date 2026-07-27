<?php

namespace LaravelApiBuilder\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Stores administrator-defined endpoint metadata and query configuration.
 */
class ApiEndpoint extends Model
{
    protected $fillable = [
        'name',
        'path',
        'method',
        'table_name',
        'description',
        'auth_required',
        'active',
        'configuration',
    ];

    protected $casts = [
        'auth_required' => 'boolean',
        'active' => 'boolean',
        'configuration' => 'array',
    ];

    public function getTable(): string
    {
        return config('api-builder.table', 'api_endpoints');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}
