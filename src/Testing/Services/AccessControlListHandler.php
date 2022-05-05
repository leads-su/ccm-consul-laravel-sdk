<?php

namespace Consul\Testing\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Promise\PromiseInterface;
use Consul\Exceptions\NotImplementedException;

/**
 * Class AccessControlListHandler
 *
 * @package Consul\Testing\Services
 */
class AccessControlListHandler extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    public function handle(): PromiseInterface
    {
        [$url, $method, $parameters] = $this->requestParameters();
        return match ($method) {
            'GET'       =>  $this->handleGetRequest($this->stripBaseUrl($url, 'acl'), $parameters),
            'POST'      =>  $this->handlePostRequest($this->stripBaseUrl($url, 'acl'), $parameters),
            'PUT'       =>  $this->handlePutRequest($this->stripBaseUrl($url, 'acl'), $parameters, $this->request->body()),
            'DELETE'    =>  $this->handleDeleteRequest($this->stripBaseUrl($url, 'acl'), $parameters),
            default     =>  throw new NotImplementedException($method . ' is not implemented!'),
        };
    }

    /**
     * Get ACL bootstrap response
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getBootstrapResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'bootstrap', [
            'ID'                =>  '527347d3-9653-07dc-adc0-598b8f2b0f4d',
            'AccessorID'        =>  'b5b1a918-50bc-fc46-dec2-d481359da4e3',
            'SecretID'          =>  '527347d3-9653-07dc-adc0-598b8f2b0f4d',
            'Description'       =>  'Bootstrap Token (Global Management)',
            'Policies'          =>  [
                [
                    'ID'        =>  '00000000-0000-0000-0000-000000000001',
                    'Name'      =>    'global-management',
                ],
            ],
            'Local'             =>  false,
            'CreateTime'        =>  '2018-10-24T10:34:20.843397-04:00',
            'Hash'              =>  'oyrov6+GFLjo/KZAfqgxF/X4J/3LX0435DOBy9V22I0=',
            'CreateIndex'       =>  12,
            'ModifyIndex'       =>  12,
        ]);
    }

    /**
     * Get replication status
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getReplicationStatus(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'replication', [
            'Enabled' => true,
            'Running' => true,
            'SourceDatacenter' => 'dc0',
            'ReplicationType' => 'tokens',
            'ReplicationIndex' => 1,
            'ReplicationTokenIndex' => 1,
            'LastSuccess' => '2021-08-26T08:00:00Z',
            'LastError' => '2021-08-25T08:00:00Z',
        ]);
    }

    /**
     * Get rules translator response
     * @param array $emulatedData
     *
     * @return string
     */
    public static function getTranslateRulesResponse(array $emulatedData): string
    {
        return Arr::get($emulatedData, 'rules/translate', 'agent_prefix "" { policy = "read" }');
    }

    /**
     * Get rules legacy translator response
     * @param array $emulatedData
     *
     * @return string
     */
    public static function getTranslateRulesLegacyResponse(array $emulatedData): string
    {
        return Arr::get($emulatedData, 'rules/translate/accessor_id', 'agent_prefix "" { policy = "read" }');
    }

    /**
     * Get response from token creation endpoint
     * @param array $emulatedData
     * @param array $parameters
     *
     * @return array
     */
    public static function getCreateTokenResponse(array $emulatedData, array $parameters): array
    {
        $baseData = Arr::get($emulatedData, 'createToken');
        return array_merge($baseData, $parameters);
    }

    /**
     * Get response from tokens list endpoint
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getListTokensResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'listTokens');
    }

    /**
     * Get response from token read endpoint
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getReadTokenResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'readToken');
    }

    /**
     * Get response from policy creation endpoint
     * @param array $emulatedData
     * @param array $parameters
     *
     * @return array
     */
    public static function getCreatePolicyResponse(array $emulatedData, array $parameters): array
    {
        $baseData = Arr::get($emulatedData, 'createPolicy');
        return array_merge($baseData, $parameters);
    }

    /**
     * Get response from policies list endpoint
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getListPoliciesResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'listPolicies');
    }

    /**
     * Get response from policy read endpoint
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getReadPolicyResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'readPolicy');
    }

    /**
     * Get response from role creation endpoint
     * @param array $emulatedData
     * @param array $parameters
     *
     * @return array
     */
    public static function getCreateRoleResponse(array $emulatedData, array $parameters): array
    {
        $baseData = Arr::get($emulatedData, 'createRole');
        return array_merge($baseData, $parameters);
    }

    /**
     * Get response from roles list endpoint
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getListRolesResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'listRoles');
    }

    /**
     * Get response from role read endpoint
     * @param array $emulatedData
     *
     * @return array
     */
    public static function getReadRoleResponse(array $emulatedData): array
    {
        return Arr::get($emulatedData, 'readRole');
    }

    /**
     * Handle GET request to ACL
     * @param string $url
     * @param array  $parameters
     *
     * @throws NotImplementedException
     * @return PromiseInterface
     */
    private function handleGetRequest(string $url, array $parameters): PromiseInterface
    {
        if ($this->isParameterPresent('dc', $parameters)) {
            if ($parameters['dc'] !== config('consul.datacenter')) {
                return Http::response('No path to datacenter', 500);
            }
        }

        return match ($url) {
            'replication'                                       =>  Http::response(self::getReplicationStatus($this->emulatedData)),
            'token/6a1253d2-1785-24fd-91c2-f8e78c745511'        =>  Http::response(self::getReadTokenResponse($this->emulatedData)),
            'token/self'                                        =>  Http::response(self::getReadTokenResponse($this->emulatedData)),
            'token/6a1253d2-1785-24fd-91c2-f8e78c745510'        =>  Http::response([], 404),
            'policy/e359bd81-baca-903e-7e64-1ccd9fdc78f4'       =>  Http::response([], 404),
            'tokens'                                            =>  Http::response(self::getListTokensResponse($this->emulatedData)),
            'policy/e359bd81-baca-903e-7e64-1ccd9fdc78f5'       =>  Http::response(self::getReadPolicyResponse($this->emulatedData)),
            'policy/name/node-read'                             =>  Http::response(self::getReadPolicyResponse($this->emulatedData)),
            'policies'                                          =>  Http::response(self::getListPoliciesResponse($this->emulatedData)),
            'role/aa770e5b-8b0b-7fcf-e5a1-8535fcc388b4'         =>  Http::response(self::getReadRoleResponse($this->emulatedData)),
            'role/aa770e5b-8b0b-7fcf-e5a1-8535fcc388b3'         =>  Http::response([], 404),
            'role/name/example-role'                            =>  Http::response(self::getReadRoleResponse($this->emulatedData)),
            'roles'                                             =>  Http::response(self::getListRolesResponse($this->emulatedData)),
            default                                             =>  throw new NotImplementedException('GET `' . $url . '` is not implemented!')
        };
    }

    /**
     * Handle POST request to ACL
     * @param string $url
     * @param array  $parameters
     *
     * @throws NotImplementedException
     * @return PromiseInterface
     */
    private function handlePostRequest(string $url, array $parameters): PromiseInterface
    {
        if ($this->isParameterPresent('dc', $parameters)) {
            if ($parameters['dc'] !== config('consul.datacenter')) {
                return Http::response('No path to datacenter', 500);
            }
        }

        return match ($url) {
            'rules/translate'                   =>  Http::response(self::getTranslateRulesResponse($this->emulatedData)),
            'rules/translate/accessor_id'       =>  Http::response(self::getTranslateRulesLegacyResponse($this->emulatedData)),
            default                             =>  throw new NotImplementedException('POST `' . $url . '` is not implemented!')
        };
    }

    /**
     * Handle PUT request to ACL
     * @param string $url
     * @param array  $parameters
     * @param string $body
     *
     * @throws NotImplementedException
     * @return PromiseInterface
     */
    private function handlePutRequest(string $url, array $parameters, string $body): PromiseInterface
    {
        if ($this->isParameterPresent('dc', $parameters)) {
            if ($parameters['dc'] !== config('consul.datacenter')) {
                return Http::response('No path to datacenter', 500);
            }
        }

        return match ($url) {
            'bootstrap'                                         =>  Http::response(self::getBootstrapResponse($this->emulatedData)),
            'token/6a1253d2-1785-24fd-91c2-f8e78c745511'        =>  Http::response(self::getCreateTokenResponse($this->emulatedData, $parameters)),
            'token/6a1253d2-1785-24fd-91c2-f8e78c745511/clone'  =>  Http::response(self::getCreateTokenResponse($this->emulatedData, $parameters)),
            'token'                                             =>  Http::response(self::getCreateTokenResponse($this->emulatedData, $parameters)),
            'token/6a1253d2-1785-24fd-91c2-f8e78c745510'        =>  Http::response([], 404),
            'token/6a1253d2-1785-24fd-91c2-f8e78c745510/clone'  =>  Http::response([], 404),
            'policy/c01a1f82-44be-41b0-a686-685fb6e0f484'       =>  Http::response([], 404),
            'policy'                                            =>  Http::response(self::getCreatePolicyResponse($this->emulatedData, $parameters)),
            'policy/c01a1f82-44be-41b0-a686-685fb6e0f485'       =>  Http::response(self::getCreatePolicyResponse($this->emulatedData, $parameters)),
            'role'                                              =>  Http::response(self::getCreateRoleResponse($this->emulatedData, $parameters)),
            'role/8bec74a4-5ced-45ed-9c9d-bca6153490bb'         =>  Http::response(self::getCreateRoleResponse($this->emulatedData, $parameters)),
            'role/8bec74a4-5ced-45ed-9c9d-bca6153490ba'         =>  Http::response([], 404),
            default                                             =>  throw new NotImplementedException('PUT `' . $url . '` is not implemented!'),
        };
    }

    /**
     * Handle DELETE request to ACL
     * @param string $url
     * @param array  $parameters
     *
     * @throws NotImplementedException
     * @return PromiseInterface
     */
    private function handleDeleteRequest(string $url, array $parameters): PromiseInterface
    {
        if ($this->isParameterPresent('dc', $parameters)) {
            if ($parameters['dc'] !== config('consul.datacenter')) {
                return Http::response('No path to datacenter', 500);
            }
        }

        return match ($url) {
            'token/8f246b77-f3e1-ff88-5b48-8ec93abf3e05'    =>  Http::response(true),
            'policy/8f246b77-f3e1-ff88-5b48-8ec93abf3e05'   =>  Http::response(true),
            'role/8f246b77-f3e1-ff88-5b48-8ec93abf3e05'     =>  Http::response(true),
            default         =>  throw new NotImplementedException('DELETE `' . $url . '` is not implemented!')
        };
    }
}
