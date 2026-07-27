# Laravel API Builder

Laravel API Builder is a reusable Laravel 12 package for creating REST endpoints from administrator-defined database configuration. It stores endpoint definitions in the database and builds runtime queries with Laravel Query Builder.

## Requirements

- PHP 8.3+
- Laravel 12
- Database driver supported by Laravel Schema and Query Builder
- `doctrine/dbal` for richer schema metadata

## Installation

Install the package:

```bash
composer require hexcj/api-builder-laravel
```

For local path development:

```json
{
  "repositories": [
    { "type": "path", "url": "packages/laravel-api-builder" }
  ],
  "require": {
    "hexcj/api-builder-laravel": "*"
  }
}
```

Publish configuration and migrations when customization is needed:

```bash
php artisan vendor:publish --tag=api-builder-config
php artisan vendor:publish --tag=api-builder-migrations
php artisan migrate
```

The package supports Laravel auto-discovery through `LaravelApiBuilder\Providers\ApiBuilderServiceProvider`.

## Configuration

`config/api-builder.php` controls route prefixes, middleware, metadata visibility, pagination limits, and whether raw expressions are allowed.

Raw expressions are disabled by default:

```php
'security' => [
    'allow_raw_expressions' => false,
    'max_limit' => 500,
    'default_limit' => 50,
],
```

Enable raw expressions only for trusted administrators.

## Database

The package creates `api_endpoints`:

- `id`
- `name`
- `path`
- `method`
- `table_name`
- `description`
- `auth_required`
- `active`
- `configuration`
- timestamps

The configuration JSON shape is:

```json
{
  "columns": [],
  "joins": [],
  "where": [],
  "with": [],
  "aggregations": [],
  "group_by": [],
  "having": [],
  "order_by": [],
  "computed": [],
  "aliases": [],
  "window": [],
  "pagination": {},
  "distinct": false,
  "limit": null,
  "offset": null
}
```

## Admin UI

Open:

```text
/api-builder
```

The UI lets administrators create endpoints with:

- Name
- Path
- Method
- Table
- Column selection loaded automatically from schema metadata
- Joins, where filters, grouping, ordering, pagination, distinct, auth, active state
- Direct JSON configuration for advanced features

Saving an endpoint only writes database records. It does not generate PHP files.

## Runtime

Dynamic runtime route:

```text
/api/{dynamicEndpoint}
```

Example:

```text
GET /api/users
```

Runtime flow:

1. Find endpoint by path and method.
2. Validate that it exists.
3. Validate that it is active.
4. Validate endpoint authentication requirement.
5. Validate table and column configuration.
6. Build a Laravel Query Builder instance.
7. Run the configured builder pipeline.
8. Execute and return JSON.

## Metadata API

```text
GET /api/builder/tables
GET /api/builder/table/users
```

Responses include table names, columns, primary key, foreign keys, and indexes when supported by the database driver.

## Query Features

### Select and Distinct

```json
{
  "columns": ["id", "name", "email"],
  "distinct": true
}
```

### Joins

Supported join types:

- `inner`
- `left`
- `right`
- `cross`

```json
{
  "joins": [
    {
      "type": "left",
      "table": "roles",
      "first": "users.role_id",
      "operator": "=",
      "second": "roles.id"
    }
  ]
}
```

### With Subselects

`with` adds related scalar data through Laravel Query Builder subselects.

```json
{
  "with": [
    {
      "alias": "latest_order_total",
      "table": "orders",
      "column": "total",
      "where_column": [
        { "first": "orders.user_id", "operator": "=", "second": "users.id" }
      ],
      "order_by": [
        { "column": "orders.created_at", "direction": "desc" }
      ],
      "limit": 1
    }
  ]
}
```

### Where

Supported operators:

- `=`
- `!=`
- `<`
- `>`
- `<=`
- `>=`
- `LIKE`
- `NOT LIKE`
- `BETWEEN`
- `NOT BETWEEN`
- `NULL`
- `NOT NULL`
- `IN`
- `NOT IN`
- `EXISTS`
- `RAW`

Nested AND/OR:

```json
{
  "where": [
    { "column": "active", "operator": "=", "value": true },
    {
      "boolean": "or",
      "nested": [
        { "column": "email", "operator": "LIKE", "value": "%@example.com" },
        { "column": "created_at", "operator": ">=", "value": "2026-01-01" }
      ]
    }
  ]
}
```

EXISTS subquery:

```json
{
  "where": [
    {
      "operator": "EXISTS",
      "table": "orders",
      "where": [
        { "column": "orders.user_id", "operator": "=", "value": 1 }
      ]
    }
  ]
}
```

### Aggregations, Group By, Having

```json
{
  "columns": ["role_id"],
  "aggregations": [
    { "function": "count", "column": "id", "alias": "users_count" }
  ],
  "group_by": ["role_id"],
  "having": [
    { "column": "users_count", "operator": ">", "value": 10 }
  ]
}
```

### Aliases

```json
{
  "aliases": [
    { "column": "users.name", "alias": "user_name" }
  ]
}
```

### Computed Columns

Requires `allow_raw_expressions=true`.

```json
{
  "computed": [
    { "expression": "CONCAT(first_name, ' ', last_name)", "alias": "full_name" }
  ]
}
```

### Window Functions

Requires `allow_raw_expressions=true`.

```json
{
  "window": [
    {
      "function": "row_number",
      "partition_by": ["role_id"],
      "order_by": [{ "column": "created_at", "direction": "desc" }],
      "alias": "row_num"
    }
  ]
}
```

### Order

```json
{
  "order_by": [
    { "column": "created_at", "direction": "desc" },
    { "column": "id", "direction": "asc" }
  ]
}
```

### Limit and Offset

```json
{
  "limit": 100,
  "offset": 200
}
```

### Pagination

Supported types:

- `paginate`
- `simplePaginate`
- `cursorPaginate`

```json
{
  "pagination": {
    "enabled": true,
    "type": "paginate",
    "per_page": 25
  }
}
```

## Authentication

Endpoints can be saved with `auth_required=true`. Runtime checks use Laravel's authenticated request user. Configure route middleware for Sanctum, Passport, or a custom middleware stack:

```php
'api_middleware' => ['api', 'auth:sanctum'],
```

For public endpoints, leave `auth_required=false`.

## OpenAPI

Generated documentation:

```text
GET /api/builder/swagger.json
```

The document is generated from active endpoint configuration.

## Architecture

- Controllers handle HTTP only.
- DTOs carry validated endpoint data.
- Repositories isolate persistence.
- Services coordinate validation, metadata, permissions, runtime execution, and OpenAPI generation.
- Builders isolate query concerns:
  - `SelectBuilder`
  - `DistinctBuilder`
  - `JoinBuilder`
  - `WithBuilder`
  - `WhereBuilder`
  - `AggregationBuilder`
  - `HavingBuilder`
  - `ComputedBuilder`
  - `AliasBuilder`
  - `WindowBuilder`
  - `OrderBuilder`
  - `PaginationBuilder`
- No runtime PHP files are generated for endpoints.

## Example Usage

Create an endpoint:

- Name: `Active Users`
- Path: `users/active`
- Method: `GET`
- Table: `users`
- Configuration:

```json
{
  "columns": ["id", "name", "email"],
  "joins": [],
  "where": [
    { "column": "active", "operator": "=", "value": true }
  ],
  "with": [],
  "aggregations": [],
  "group_by": [],
  "having": [],
  "order_by": [
    { "column": "created_at", "direction": "desc" }
  ],
  "computed": [],
  "aliases": [],
  "window": [],
  "pagination": {
    "enabled": true,
    "type": "paginate",
    "per_page": 25
  },
  "distinct": false,
  "limit": null,
  "offset": null
}
```

Call:

```text
GET /api/users/active
```

## Testing Strategy

Recommended coverage:

- Unit test each builder with SQLite and assert generated Query Builder bindings/results.
- Feature test metadata endpoints against test migrations.
- Feature test endpoint create/update validation.
- Feature test dynamic route execution for active, inactive, missing, public, and authenticated endpoints.
- Feature test OpenAPI generation from saved endpoint definitions.
- Security tests for invalid identifiers, invalid operators, duplicate paths, missing tables, missing columns, and disabled raw expressions.

Run package tests:

```bash
composer test
```
