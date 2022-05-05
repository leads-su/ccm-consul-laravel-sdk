<?php

namespace Consul\Services\KeyValue;

use Consul\Abstracts\AbstractService;
use Consul\Services\KeyValue\Interfaces\KeyValueInterface;

/**
 * Class KeyValue
 *
 * @package Consul\Services\KeyValue
 */
class KeyValue extends AbstractService implements KeyValueInterface
{
    /**
     * @inheritDoc
     */
    public function get(string $key, array $options = []): array
    {
        return $this->processGetRequest(sprintf('kv/%s', $key), $this->resolveOptions($options, [
            'dc', 'recurse', 'raw', 'keys',
            'separator', 'ns',
        ]));
    }

    /**
     * @inheritDoc
     */
    public function put(string $key, mixed $value, array $options = []): bool
    {
        return $this->processPutRequest(sprintf('kv/%s', $key), $value, $this->resolveOptions($options, [
            'dc', 'flags', 'cas', 'acquire',
            'release', 'ns',
        ]));
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key, array $options = []): bool
    {
        return $this->processDeleteRequest(sprintf('kv/%s', $key), $this->resolveOptions($options, [
            'dc', 'recurse',
        ]));
    }
}
