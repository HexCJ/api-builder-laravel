<?php

namespace LaravelApiBuilder\Tests;

use LaravelApiBuilder\Providers\ApiBuilderServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Base package test case for Orchestra Testbench.
 */
abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [ApiBuilderServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('api-builder.builder_middleware', ['web']);
    }
}
