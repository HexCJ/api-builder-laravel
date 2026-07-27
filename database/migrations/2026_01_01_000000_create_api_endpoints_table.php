<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('api-builder.table', 'api_endpoints'), function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('path')->unique();
            $table->string('method', 10)->default('GET');
            $table->string('table_name');
            $table->text('description')->nullable();
            $table->boolean('auth_required')->default(false);
            $table->boolean('active')->default(true);
            $table->json('configuration');
            $table->timestamps();

            $table->index(['path', 'method', 'active']);
            $table->index('table_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('api-builder.table', 'api_endpoints'));
    }
};
