<?php

namespace Consul\Testing\Services;

use Illuminate\Support\Arr;
use Illuminate\Http\Client\Request;
use GuzzleHttp\Promise\PromiseInterface;
use Consul\Exceptions\NotImplementedException;

/**
 * Class AbstractHandler
 *
 * @package Consul\Testing\Services
 */
abstract class AbstractHandler
{
    /**
     * Http client request
     * @var Request
     */
    protected Request $request;

    /**
     * Variable which holds path to emulator file
     * @var string
     */
    protected string $emulator;

    /**
     * Variable which holds content of emulator file
     * @var mixed
     */
    protected mixed $emulatedData;

    /**
     * KeyValueHandler Constructor.
     *
     * @param Request $request
     * @param string  $emulator
     * @param mixed   $emulatedData
     */
    public function __construct(Request $request, string $emulator, mixed $emulatedData)
    {
        $this->request = $request;
        $this->emulator = $emulator;
        $this->emulatedData = $emulatedData;
    }

    /**
     * Handle incoming request
     * @return PromiseInterface
     * @throws NotImplementedException
     */
    abstract public function handle(): PromiseInterface;

    /**
     * Get request parameters
     * @return array
     */
    protected function requestParameters(): array
    {
        return [
            $this->request->url(),
            $this->request->method(),
            $this->request->data(),
        ];
    }

    /**
     * Strip Base URL
     * @param string $url
     * @param string $endpoint
     *
     * @return string
     */
    protected function stripBaseUrl(string $url, string $endpoint): string
    {
        return Arr::first(explode('?', str_replace(sprintf(
            '%s://%s:%d/v1/%s/',
            config('consul.connections.default.scheme'),
            config('consul.connections.default.host'),
            config('consul.connections.default.port'),
            $endpoint
        ), '', $url)));
    }

    /**
     * Check if specified parameter is present
     * @param string $parameter
     * @param array  $parameters
     *
     * @return bool
     */
    protected function isParameterPresent(string $parameter, array $parameters = []): bool
    {
        return isset($parameter[$parameter]) || array_key_exists($parameter, $parameters);
    }

    /**
     * Check if specified parameter is present and equals to parameter value from config
     * @param string $parameter
     * @param string $configRef
     * @param array  $parameters
     *
     * @return bool
     */
    protected function isParameterPresentAndSameAsConfig(string $parameter, string $configRef, array $parameters = []): bool
    {
        if (!$this->isParameterPresent($parameter, $parameters)) {
            return true;
        }
        return config($configRef) === $parameters[$parameter];
    }
}
