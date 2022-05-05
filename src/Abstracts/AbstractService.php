<?php

namespace Consul\Abstracts;

use Exception;
use Consul\Helpers\Str;
use Illuminate\Support\Arr;
use Consul\Helpers\OptionsResolver;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Consul\Exceptions\RequestException;
use Illuminate\Http\Client\PendingRequest;

/**
 * Class AbstractService
 *
 * @package Consul\Abstracts
 */
abstract class AbstractService
{
    /**
     * Pending Request instance
     * @var PendingRequest
     */
    private PendingRequest $client;

    /**
     * AbstractService Constructor.
     *
     * @return void
     */
    public function __construct(?PendingRequest $client = null)
    {
        if ($client !== null) {
            $this->client = $client;
        } else {
            $activeConnection = $this->getActiveConnection();
            $scheme = Arr::get($activeConnection, 'scheme', 'http');
            $host = Arr::get($activeConnection, 'host', '127.0.0.1');
            $port = Arr::get($activeConnection, 'port', 8500);


            $this->client = Http::baseUrl(sprintf(
                '%s://%s:%d/v1',
                $scheme,
                $host,
                $port,
            ))->withHeaders([
                'X-Consul-Token'    =>  Arr::get($activeConnection, 'access_token') ?? config('consul.access_token', ''),
            ])->timeout(config('consul.timeout', 15));
        }
    }

    /**
     * Get client instance
     * @return PendingRequest
     */
    protected function client(): PendingRequest
    {
        return $this->client;
    }

    /**
     * Process `GET` request
     * @param string $path
     * @param array  $parameters
     *
     * @throws RequestException
     * @return mixed
     */
    protected function processGetRequest(string $path, array $parameters = []): mixed
    {
        return $this->processRequest($this->client()->get($path, $parameters), $parameters);
    }

    /**
     * Process `POST` request
     *
     * @param string $path
     * @param mixed  $body
     * @param array  $parameters
     *
     * @throws RequestException
     * @return mixed
     */
    protected function processPostRequest(string $path, mixed $body, array $parameters = []): mixed
    {
        return $this->processSendRequest('POST', $path, [
            'body'      =>  $body,
            'query'     =>  $parameters,
        ], $parameters);
    }

    /**
     * Process `PUT` request
     *
     * @param string $path
     * @param mixed  $body
     * @param array  $parameters
     *
     * @throws RequestException
     * @return mixed
     */
    protected function processPutRequest(string $path, mixed $body = null, array $parameters = []): mixed
    {
        $requestOptions = [
            'headers'       =>  [],
            'query'         =>  $parameters,
        ];

        if ($body === null) {
            Arr::forget($requestOptions, 'body');
        } elseif (Str::isJson($body)) {
            $requestOptions['headers']['Content-Type'] = 'application/json';
            Arr::set($requestOptions, 'json', json_decode($body, true));
        } elseif (is_array($body) && count($body) > 0) {
            $requestOptions['headers']['Content-Type'] = 'application/json';
            Arr::set($requestOptions, 'json', $body);
        } else {
            Arr::set($requestOptions, 'body', $body);
        }

        if (Arr::exists($requestOptions, 'body')) {
            $bodyValue = Arr::get($requestOptions, 'body');
            if (
                is_array($bodyValue) &&
                count($bodyValue) === 0
            ) {
                Arr::forget($requestOptions, 'body');
            }
        }

        return $this->processSendRequest('PUT', $path, $requestOptions, $parameters);
    }

    /**
     * Process `DELETE` request
     * @param string $path
     * @param array  $parameters
     *
     * @throws RequestException
     * @return bool
     */
    protected function processDeleteRequest(string $path, array $parameters = []): bool
    {
        return $this->processRequest($this->client()->delete($path, $parameters), $parameters);
    }

    /**
     * Process SEND request method
     * @param string $method
     * @param string $path
     * @param array  $configuration
     * @param array  $parameters
     *
     * @throws RequestException|Exception
     * @return mixed
     */
    protected function processSendRequest(string $method, string $path, array $configuration, array $parameters = []): mixed
    {
        return $this->processRequest($this->client()->send($method, $path, $configuration), $parameters);
    }

    /**
     * Resolve options and generate appropriate array
     * @param array  $options
     * @param array  $availableOptions
     *
     * @return array
     */
    protected function resolveOptions(array $options = [], array $availableOptions = []): array
    {
        return OptionsResolver::resolve($options, $availableOptions);
    }


    /**
     * Process received response from Consul server
     *
     * @param Response $response
     * @param array    $parameters
     *
     * @throws RequestException
     * @return mixed
     */
    private function processRequest(Response $response, array $parameters = []): mixed
    {
        if ($response->failed()) {
            throw \Consul\Helpers\Http::responseToException($response);
        }
        $responseBody = $response->body();
        if ($responseBody === '1' || $responseBody === '0') {
            return $responseBody === '1';
        }

        if ($responseBody === '{}' || $responseBody === "") {
            return [];
        }

        $jsonResponse = $response->json();
        if (!$jsonResponse) {
            return $response->body();
        }
        return $jsonResponse;
    }

    /**
     * Get active consul connection
     * @return array
     */
    private function getActiveConnection(): array
    {
        $connectionName = config('consul.default', 'default');
        $connectionsList = config('consul.connections', [
            'default'       =>  [
                'scheme'    =>  'http',
                'host'      =>  '127.0.0.1',
                'port'      =>  8500,
            ],
        ]);

        $withAutoSelect = config('consul.auto_select', false);
        $withRandomServer = config('consul.use_random', false);

        if ($withRandomServer) {
            return Arr::random($connectionsList);
        }


        if ($withAutoSelect) {
            if (Cache::has('consul::server:connection')) {
                $connection = Cache::get('consul::server:connection');
                return $connectionsList[$connection];
            }

            $onlineServers = [];
            foreach ($connectionsList as $name => $details) {
                if ($this->serverOnline(
                    Arr::get($details, 'host'),
                    Arr::get($details, 'port'),
                )) {
                    Arr::set($onlineServers, $name, $name);
                }
            }

            $currentServer = Arr::random($onlineServers);
            Cache::set('consul::server:connection', $currentServer);
            return $currentServer;
        }

        return $connectionsList[$connectionName];
    }

    /**
     * Check if requested server is online
     * @param string $host
     * @param int    $port
     *
     * @return bool
     */
    private function serverOnline(string $host, int $port): bool
    {
        if (App::environment('testing')) {
            return true;
        }

        $online = false;
        if ($handle = fsockopen($host, $port, $errorCode, $errorString, 2)) {
            $online = true;
        }
        fclose($handle);
        return $online;
    }
}
