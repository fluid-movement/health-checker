<?php

namespace App\Service;

readonly class ConfigService
{
    public function __construct(private array $config = [])
    {
    }

    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }
}
