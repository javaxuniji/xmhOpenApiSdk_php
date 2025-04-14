<?php

namespace XMH\OpenApiSdk;

interface StorageInterface
{
    /**
     * Get a value by key
     *
     * @param string $key
     * @return string|null
     */
    public function get(string $key): ?string;

    /**
     * Set a value with key and expiration
     *
     * @param string $key
     * @param string $value
     * @param int $ttl Expiration time in seconds
     * @return void
     */
    public function set(string $key, string $value, int $ttl = 3600): void;

    /**
     * Delete a value by key
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;
}