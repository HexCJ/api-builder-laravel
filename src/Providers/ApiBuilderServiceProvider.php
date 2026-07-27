<?php

namespace LaravelApiBuilder\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use LaravelApiBuilder\Builders\AggregationBuilder;
use LaravelApiBuilder\Builders\AliasBuilder;
use LaravelApiBuilder\Builders\ComputedBuilder;
use LaravelApiBuilder\Builders\DistinctBuilder;
use LaravelApiBuilder\Builders\HavingBuilder;
use LaravelApiBuilder\Builders\JoinBuilder;
use LaravelApiBuilder\Builders\OrderBuilder;
use LaravelApiBuilder\Builders\QueryPipeline;
use LaravelApiBuilder\Builders\SelectBuilder;
use LaravelApiBuilder\Builders\WhereBuilder;
use LaravelApiBuilder\Builders\WithBuilder;
use LaravelApiBuilder\Builders\WindowBuilder;
use LaravelApiBuilder\Contracts\EndpointRepositoryContract;
use LaravelApiBuilder\Contracts\MetadataServiceContract;
use LaravelApiBuilder\Repositories\EndpointRepository;
use LaravelApiBuilder\Services\EndpointService;
use LaravelApiBuilder\Services\MetadataService;

/**
 * Registers package services, routes, configuration, migrations, and views.
 */
class ApiBuilderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/api-builder.php', 'api-builder');

        $this->app->bind(EndpointRepositoryContract::class, EndpointRepository::class);
        $this->app->bind(MetadataServiceContract::class, MetadataService::class);
        $this->app->alias(EndpointService::class, 'api-builder');

        $this->app->singleton(QueryPipeline::class, function ($app): QueryPipeline {
            return new QueryPipeline([
                $app->make(SelectBuilder::class),
                $app->make(DistinctBuilder::class),
                $app->make(JoinBuilder::class),
                $app->make(WithBuilder::class),
                $app->make(AggregationBuilder::class),
                $app->make(AliasBuilder::class),
                $app->make(ComputedBuilder::class),
                $app->make(WindowBuilder::class),
                $app->make(WhereBuilder::class),
                $app->make(HavingBuilder::class),
                $app->make(OrderBuilder::class),
            ]);
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'api-builder');

        $this->publishes([
            __DIR__.'/../../config/api-builder.php' => config_path('api-builder.php'),
        ], 'api-builder-config');

        $this->publishes([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ], 'api-builder-migrations');

        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/api-builder'),
        ], 'api-builder-views');

        Route::middleware(config('api-builder.builder_middleware', ['web', 'auth']))
            ->prefix(config('api-builder.builder_prefix', 'api-builder'))
            ->group(__DIR__.'/../../routes/web.php');

        Route::middleware(config('api-builder.api_middleware', ['api']))
            ->prefix(config('api-builder.route_prefix', 'api'))
            ->group(__DIR__.'/../../routes/api.php');
    }
}
