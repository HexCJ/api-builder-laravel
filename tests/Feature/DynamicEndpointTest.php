<?php

namespace LaravelApiBuilder\Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use LaravelApiBuilder\Models\ApiEndpoint;
use LaravelApiBuilder\Support\ConfigurationDefaults;
use LaravelApiBuilder\Tests\TestCase;

/**
 * Covers execution of saved dynamic endpoint definitions.
 */
class DynamicEndpointTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate')->run();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(true);
        });

        DB::table('users')->insert([
            ['name' => 'Ada', 'active' => true],
            ['name' => 'Linus', 'active' => false],
        ]);
    }

    public function test_dynamic_endpoint_executes_saved_query_configuration(): void
    {
        ApiEndpoint::query()->create([
            'name' => 'Active users',
            'path' => 'users/active',
            'method' => 'GET',
            'table_name' => 'users',
            'auth_required' => false,
            'active' => true,
            'configuration' => array_replace(ConfigurationDefaults::values(), [
                'columns' => ['id', 'name'],
                'where' => [
                    ['column' => 'active', 'operator' => '=', 'value' => true],
                ],
            ]),
        ]);

        $this->getJson('/api/users/active')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Ada')
            ->assertJsonMissing(['name' => 'Linus']);
    }
}
