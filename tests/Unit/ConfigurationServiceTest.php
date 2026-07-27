<?php

namespace LaravelApiBuilder\Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LaravelApiBuilder\Exceptions\InvalidConfigurationException;
use LaravelApiBuilder\Services\ConfigurationService;
use LaravelApiBuilder\Tests\TestCase;

/**
 * Covers endpoint configuration validation behavior.
 */
class ConfigurationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('email');
            $table->boolean('active');
            $table->timestamps();
        });
    }

    public function test_it_accepts_valid_configuration(): void
    {
        app(ConfigurationService::class)->validate('users', 'GET', [
            'columns' => ['id', 'email'],
            'where' => [
                ['column' => 'active', 'operator' => '=', 'value' => true],
            ],
        ]);

        $this->assertTrue(true);
    }

    public function test_it_rejects_invalid_operator(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        app(ConfigurationService::class)->validate('users', 'GET', [
            'where' => [
                ['column' => 'active', 'operator' => 'MATCHES', 'value' => true],
            ],
        ]);
    }
}
