<?php

namespace LaravelApiBuilder\Builders;

use Illuminate\Database\Query\Builder;
use LaravelApiBuilder\Exceptions\InvalidConfigurationException;

/**
 * Executes a configured query with limit, offset, or Laravel pagination strategies.
 */
class PaginationBuilder
{
    public function execute(Builder $query, array $configuration): mixed
    {
        $pagination = $configuration['pagination'] ?? [];
        $maxLimit = (int) config('api-builder.security.max_limit', 500);

        if (($configuration['limit'] ?? null) !== null) {
            $query->limit(min((int) $configuration['limit'], $maxLimit));
        }

        if (($configuration['offset'] ?? null) !== null) {
            $query->offset((int) $configuration['offset']);
        }

        if ($pagination === [] || ($pagination['enabled'] ?? false) === false) {
            return $query->get();
        }

        $perPage = min((int) ($pagination['per_page'] ?? config('api-builder.security.default_limit', 50)), $maxLimit);

        return match ($pagination['type'] ?? 'paginate') {
            'paginate' => $query->paginate($perPage),
            'simplePaginate' => $query->simplePaginate($perPage),
            'cursorPaginate' => $query->cursorPaginate($perPage),
            default => throw new InvalidConfigurationException('Unsupported pagination type.'),
        };
    }
}
