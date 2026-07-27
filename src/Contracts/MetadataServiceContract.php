<?php

namespace LaravelApiBuilder\Contracts;

interface MetadataServiceContract
{
    /**
     * @return array<int, string>
     */
    public function tables(): array;

    /**
     * @return array<string, mixed>
     */
    public function table(string $table): array;
}
