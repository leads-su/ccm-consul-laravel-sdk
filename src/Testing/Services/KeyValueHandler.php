<?php

namespace Consul\Testing\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Promise\PromiseInterface;
use Consul\Exceptions\NotImplementedException;

/**
 * Class KeyValueHandler
 *
 * @package Consul\Testing\Services
 */
class KeyValueHandler extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    public function handle(): PromiseInterface
    {
        [$url, $method, $parameters] = $this->requestParameters();
        $key = $this->getKeyFromUrl($url);

        return match ($method) {
            'GET'       =>  $this->handleGetRequest($key, $parameters),
            'PUT'       =>  $this->handlePutRequest($key, $parameters, $this->request->body()),
            'DELETE'    =>  $this->handleDeleteRequest($key, $parameters),
            default     =>  throw new NotImplementedException($method . ' is not implemented!'),
        };
    }

    /**
     * Get data for plain key request
     *
     * @param array  $emulatedData
     * @param string $key
     * @param bool   $exact
     *
     * @return array[]
     */
    public static function getKeyResponseData(array $emulatedData, string $key, bool $exact = false): array
    {
        $responseData = [];

        foreach ($emulatedData as $entry) {
            $entryKey = Arr::get($entry, 'Key');
            if ($key === $entryKey) {
                if ($exact) {
                    $responseData = $entry;
                } else {
                    $responseData[] = $entry;
                }
                break;
            }
        }

        return $responseData;
    }

    /**
     * Get data for 'keys' parameter response
     *
     * @param array  $emulatedData
     * @param string $key
     *
     * @return string[]
     */
    public static function getKeysResponseData(array $emulatedData, string $key): array
    {
        $keys = [];
        foreach ($emulatedData as $entry) {
            $entryKey = Arr::get($entry, 'Key');
            if (Str::startsWith($entryKey, $key)) {
                $keys[] = Arr::get($entry, 'Key');
            }
        }
        return $keys;
    }

    /**
     * Get data for 'recurse' parameter response
     *
     * @param array  $emulatedData
     * @param string $key
     *
     * @return array[]
     */
    public static function getRecurseResponseData(array $emulatedData, string $key): array
    {
        $responseData = [];
        foreach ($emulatedData as $entry) {
            $entryKey = Arr::get($entry, 'Key');
            if (Str::startsWith($entryKey, $key)) {
                $responseData[] = $entry;
            }
        }
        return $responseData;
    }

    /**
     * Get data for 'raw' parameter response
     *
     * @param array  $emulatedData
     * @param string $key
     *
     * @return mixed
     */
    public static function getRawResponseData(array $emulatedData, string $key): mixed
    {
        $data = self::getKeyResponseData($emulatedData, $key, true);
        return base64_decode(Arr::get($data, 'Value'));
    }

    /**
     * Get last index from emulator
     * @param array $emulatedData
     *
     * @return int
     */
    public static function getLastIndex(array $emulatedData): int
    {
        $index = 0;

        foreach ($emulatedData as $entry) {
            $entryIndex = Arr::get($entry, 'CreateIndex');
            if ($entryIndex > $index) {
                $index = $entryIndex;
            }
        }

        return $index;
    }

    /**
     * Get requested key from URL
     * @param string $url
     *
     * @return string
     */
    private function getKeyFromUrl(string $url): string
    {
        return Arr::first(explode('?', str_replace(sprintf(
            '%s://%s:%d/v1/kv/',
            config('consul.connections.default.scheme', 'http'),
            config('consul.connections.default.host', '127.0.0.1'),
            config('consul.connections.default.port', 8500),
        ), '', $url)));
    }

    /**
     * Check if specified key exists on the "API"
     *
     * @param string $key
     *
     * @return bool
     */
    private function isKeyPresent(string $key): bool
    {
        $exists = false;

        foreach ($this->emulatedData as $entry) {
            $entryKey = Arr::get($entry, 'Key');

            if (
                $entryKey === $key ||
                Str::startsWith($entryKey, $key)
            ) {
                $exists = true;
                break;
            }
        }
        return $exists;
    }

    /**
     * Check if specified key is empty
     * @param string $key
     *
     * @return bool
     */
    private function isKeyEmpty(string $key): bool
    {
        return trim($key) === '';
    }

    /**
     * Handle GET request to KV
     * @param string $key
     * @param array  $parameters
     *
     * @return PromiseInterface
     */
    private function handleGetRequest(string $key, array $parameters = []): PromiseInterface
    {
        if ($this->isKeyEmpty($key)) {
            return Http::response('Missing key name', 400);
        }

        if (!$this->isKeyPresent($key)) {
            return Http::response([], 404);
        }

        if ($this->isParameterPresent('dc', $parameters)) {
            if ($parameters['dc'] !== config('consul.datacenter')) {
                return Http::response('No path to datacenter', 500);
            }
        }

        if ($this->isParameterPresent('keys', $parameters)) {
            return Http::response(self::getKeysResponseData($this->emulatedData, $key));
        }

        if ($this->isParameterPresent('recurse', $parameters)) {
            return Http::response(self::getRecurseResponseData($this->emulatedData, $key));
        }

        if ($this->isParameterPresent('raw', $parameters)) {
            return Http::response(self::getRawResponseData($this->emulatedData, $key));
        }

        return Http::response(self::getKeyResponseData($this->emulatedData, $key));
    }

    /**
     * Handle PUT request to KV
     * @param string $key
     * @param array  $parameters
     * @param string $body
     *
     * @return PromiseInterface
     */
    private function handlePutRequest(string $key, array $parameters, string $body): PromiseInterface
    {
        if ($key === 'fail') {
            return Http::response([], 400);
        }

        if ($this->isKeyEmpty($key)) {
            return Http::response('Missing key name', 400);
        }

        if (!$this->isParameterPresentAndSameAsConfig('dc', 'consul.datacenter', $parameters)) {
            return Http::response('No path to datacenter', 500);
        }

        if (!$this->isKeyPresent($key)) {
            $lastIndex = self::getLastIndex($this->emulatedData) + 1;
            $this->emulatedData[] = [
                'LockIndex'     => 0,
                'Key'           => $key,
                'Flags'         => 0,
                'Value'         => base64_encode($body),
                'CreateIndex'   => $lastIndex,
                'ModifyIndex'   => $lastIndex,
            ];
        } else {
            foreach ($this->emulatedData as $index => $entry) {
                $entryKey = Arr::get($entry, 'Key');
                if ($key === $entryKey) {
                    $entry['Value'] = base64_encode($body);
                    $this->emulatedData[$index] = $entry;
                }
            }
        }
        file_put_contents($this->emulator, json_encode([
            'emulates'      =>  'kv',
            'provides'      =>  $this->emulatedData,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return Http::response(true);
    }

    /**
     * Handle DELETE request to KV
     * @param string $key
     * @param array  $parameters
     *
     * @return PromiseInterface
     */
    private function handleDeleteRequest(string $key, array $parameters = []): PromiseInterface
    {
        if ($this->isKeyEmpty($key)) {
            return Http::response('Missing key name', 400);
        }

        if (!$this->isKeyPresent($key)) {
            return Http::response(true, 200);
        }

        if ($this->isParameterPresent('dc', $parameters)) {
            if ($parameters['dc'] !== config('consul.datacenter')) {
                return Http::response('No path to datacenter', 500);
            }
        }

        foreach ($this->emulatedData as $index => $entry) {
            $entryKey = Arr::get($entry, 'Key');
            if ($key === $entryKey) {
                unset($this->emulatedData[$index]);
            }
        }

        file_put_contents($this->emulator, json_encode([
            'emulates'      =>  'kv',
            'provides'      =>  $this->emulatedData,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return Http::response(true);
    }
}
