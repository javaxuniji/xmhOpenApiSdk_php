<?php

namespace XMH\OpenApiSdk;

class MemoryStorage implements StorageInterface
{
    private array $storage = [];
    private array $expiration = [];

    public function get(string $key): ?string
    {
        if (!isset($this->storage[$key])) {
            return null;
        }

        if (isset($this->expiration[$key]) && time() > $this->expiration[$key]) {
            $this->delete($key);
            return null;
        }

        return $this->storage[$key];
    }

    public function set(string $key, string $value, int $ttl = 7200): void
    {
        $this->storage[$key] = $value;
        $this->expiration[$key] = time() + $ttl;
    }

    public function delete(string $key): void
    {
        unset($this->storage[$key], $this->expiration[$key]);
    }
}