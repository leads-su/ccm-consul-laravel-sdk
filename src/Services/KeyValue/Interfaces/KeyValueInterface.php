<?php

namespace Consul\Services\KeyValue\Interfaces;

use Consul\Exceptions\RequestException;

/**
 * Interface KeyValueInterface
 *
 * @package Consul\Services\KeyValue\Interfaces
 */
interface KeyValueInterface
{
    /**
     * This endpoint returns the specified key.
     * If no key exists at the given path, a 404 is returned instead of a 200 response.
     * For multi-key reads, please consider using transaction.
     *
     * @param string $key
     * @param array  $options
     *
     * @return array
     * @throws RequestException
     */
    public function get(string $key, array $options = []): array;

    /**
     * This endpoint updates the value of the specified key.
     * If no key exists at the given path, the key will be created.
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $options
     *
     * @return bool
     * @throws RequestException
     */
    public function put(string $key, mixed $value, array $options = []): bool;

    /**
     * This endpoint deletes a single key or all keys sharing a prefix.
     *
     * @param string $key
     * @param array  $options
     *
     * @return bool
     * @throws RequestException
     */
    public function delete(string $key, array $options = []): bool;
}
