<?php

namespace Consul\Testing\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Promise\PromiseInterface;
use Consul\Exceptions\NotImplementedException;

/**
 * Class AgentHandler
 *
 * @package Consul\Testing\Services
 */
class AgentHandler extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    public function handle(): PromiseInterface
    {
        [$url, $method, $parameters] = $this->requestParameters();
        return match ($method) {
            'GET'       =>  $this->handleGetRequest($this->stripBaseUrl($url, 'agent'), $parameters),
            'POST'      =>  $this->handlePostRequest($this->stripBaseUrl($url, 'acl'), $parameters),
            'PUT'       =>  $this->handlePutRequest($this->stripBaseUrl($url, 'agent'), $parameters, $this->request->body()),
            'DELETE'    =>  $this->handleDeleteRequest($this->stripBaseUrl($url, 'agent'), $parameters),
            default     =>  throw new NotImplementedException($method . ' is not implemented!'),
        };
    }

    /**
     * Get response for `host` request
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getHostResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'host');
    }

    /**
     * Get response for `members` request
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getMembersResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'members');
    }

    /**
     * Get response for `self` request
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getSelfResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'self');
    }

    /**
     * Get response for `self` request
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getMetricsResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'metrics');
    }

    /**
     * Get response for `monitor` request
     * @param array $emulatedData
     *
     * @return mixed
     */
    public static function getMonitorResponse(array $emulatedData): mixed
    {
        return Arr::get($emulatedData, 'streamLogsText');
    }

    /**
     * Get response for `checks` request
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getListChecksResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'checks');
    }

    /**
     * Get response for `services` request
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getListServicesResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'services');
    }

    /**
     * Get response from `service/_id_` request
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getServiceConfigurationResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'serviceConfiguration');
    }

    /**
     * Get response from service health request
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getServiceHealthResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'serviceHealth');
    }

    /**
     * Handle GET request to Agent
     * @param string $url
     * @param array  $parameters
     *
     * @throws NotImplementedException
     * @return PromiseInterface
     */
    private function handleGetRequest(string $url, array $parameters = []): PromiseInterface
    {
        return match ($url) {
            'host'                              =>  Http::response(self::getHostResponse($this->emulatedData)),
            'members'                           =>  Http::response(self::getMembersResponse($this->emulatedData)),
            'self'                              =>  Http::response(self::getSelfResponse($this->emulatedData)),
            'metrics'                           =>  Http::response(self::getMetricsResponse($this->emulatedData)),
            'monitor'                           =>  Http::response(self::getMonitorResponse($this->emulatedData)),
            'checks'                            =>  Http::response(self::getListChecksResponse($this->emulatedData)),
            'services'                          =>  Http::response(self::getListServicesResponse($this->emulatedData)),
            'service/redis1'                    =>  Http::response(self::getServiceConfigurationResponse($this->emulatedData)),
            'health/service/name/redis'         =>  Http::response(self::getServiceHealthResponse($this->emulatedData)),
            'health/service/id/redis1'          =>  Http::response(Arr::first(self::getServiceHealthResponse($this->emulatedData))),
            default             =>  throw new NotImplementedException('GET `' . $url . '` is not implemented!'),
        };
    }

    /**
     * Handle POST request to Agent
     * @param string $url
     * @param array  $parameters
     *
     * @throws NotImplementedException
     * @return PromiseInterface
     */
    private function handlePostRequest(string $url, array $parameters): PromiseInterface
    {
        return match ($url) {
            default     =>  throw new NotImplementedException('POST `' . $url . '` is not implemented!'),
        };
    }

    /**
     * Handle PUT request to Agent
     * @param string $url
     * @param array  $parameters
     * @param string $body
     *
     * @throws NotImplementedException
     * @return PromiseInterface
     */
    private function handlePutRequest(string $url, array $parameters = [], string $body = ''): PromiseInterface
    {
        return match ($url) {
            'check/register'                =>  Arr::exists($parameters, 'ID') ? Http::response('') : Http::response('', 400),
            'check/deregister/ping_google'  =>  Http::response(''),
            'check/deregister/ping_google'  =>  Http::response(''),
            'check/pass/ping_google'        =>  Http::response(''),
            'check/fail/ping_google'        =>  Http::response(''),
            'check/warn/ping_google'        =>  Http::response(''),
            'check/pass/check_google'       =>  Http::response('', 500),
            'check/fail/check_google'       =>  Http::response('', 500),
            'check/warn/check_google'       =>  Http::response('', 500),
            'service/register'              =>  Http::response(''),
            'service/maintenance/redis1'    =>  Http::response(''),
            'service/deregister/redis1'     =>  Http::response(''),
            default                         =>  throw new NotImplementedException('PUT `' . $url . '` is not implemented!'),
        };
    }

    /**
     * Handle DELETE request to Agent
     * @param string $url
     * @param array  $parameters
     *
     * @throws NotImplementedException
     * @return PromiseInterface
     */
    private function handleDeleteRequest(string $url, array $parameters = []): PromiseInterface
    {
        return match ($url) {
            default     =>  throw new NotImplementedException('DELETE `' . $url . '` is not implemented!'),
        };
    }
}
