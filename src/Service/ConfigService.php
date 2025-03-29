<?php

namespace App\Service;

class ConfigService
{
    private array $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function get(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function getGroup(string $group): array
    {
        return $this->config[$group] ?? [];
    }

    public function has(string $key): bool
    {
        return isset($this->config[$key]);
    }
}
