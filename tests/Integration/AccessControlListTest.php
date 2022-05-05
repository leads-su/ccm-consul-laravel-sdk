<?php

namespace Consul\Test\Integration;

use Consul\Test\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Consul\Exceptions\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Consul\Testing\Services\AccessControlListHandler;
use Consul\Services\AccessControlList\AccessControlList;
use Consul\Services\AccessControlList\Interfaces\AccessControlListInterface;

/**
 * Class AccessControlListTest
 *
 * @package Consul\Test\Integration
 */
class AccessControlListTest extends TestCase
{
    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfBootstrapReturnedValidData(): void
    {
        $response = $this->service()->bootstrap();
        $this->assertEquals(AccessControlListHandler::getBootstrapResponse($this->getEmulatedData('acl')), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfReplicationStatusFailedToConnectToDatacenter(): void
    {
        $this->expectInternalServerError();
        $this->service()->replicationStatus('invalid_datacenter');
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfReplicationStatusReturnedValidData(): void
    {
        $response = $this->service()->replicationStatus('dc0');
        $this->assertEquals(AccessControlListHandler::getReplicationStatus($this->getEmulatedData('acl')), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfRulesTranslatorReturnedValidData(): void
    {
        $response = $this->service()->translateRule('agent "" { policy = "read" }');
        $this->assertEquals(AccessControlListHandler::getTranslateRulesResponse($this->getEmulatedData('acl')), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfLegacyRulesTranslatorReturnedValidData(): void
    {
        $response = $this->service()->translateLegacyRule('accessor_id');
        $this->assertEquals(AccessControlListHandler::getTranslateRulesLegacyResponse($this->getEmulatedData('acl')), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfTokenCanBeCreatedWithValidData(): void
    {
        $parameters = [
            'Description'       =>  "Agent token for 'node1'",
            'Policies'          =>  [
                [
                    'ID'        =>  '165d4317-e379-f732-ce70-86278c4558f7',
                ],
                [
                    'Name'      =>  'node-read',
                ],
            ],
            'Local'             =>  false,
        ];

        $response = $this->service()->createToken($parameters);
        $this->assertEquals(AccessControlListHandler::getCreateTokenResponse($this->getEmulatedData('acl'), $parameters), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfReadTokenReturnedValidData(): void
    {
        $response = $this->service()->readToken('6a1253d2-1785-24fd-91c2-f8e78c745511');
        $this->assertEquals(AccessControlListHandler::getReadTokenResponse($this->getEmulatedData('acl')), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfReadTokenReturnedInvalidData(): void
    {
        $this->expectNotFound();
        $this->service()->readToken('6a1253d2-1785-24fd-91c2-f8e78c745510');
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfMyTokenReturnedValidData(): void
    {
        $response = $this->service()->myToken();
        $this->assertEquals(AccessControlListHandler::getReadTokenResponse($this->getEmulatedData('acl')), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfUpdateTokenReturnedValidData(): void
    {
        $parameters = [
            'Description'       =>  "Agent token for 'node1'",
            'Policies'          =>  [
                [
                    'ID'        =>  '165d4317-e379-f732-ce70-86278c4558f7',
                ],
                [
                    'Name'      =>  'node-read',
                ],
                [
                    'Name'      =>  'service-read',
                ],
            ],
            'Local'             =>  false,
        ];
        $response = $this->service()->updateToken('6a1253d2-1785-24fd-91c2-f8e78c745511', $parameters);
        $this->assertEquals(AccessControlListHandler::getCreateTokenResponse($this->getEmulatedData('acl'), $parameters), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfUpdateTokenFailedToUpdateSpecifiedToken(): void
    {
        $this->expectNotFound();
        $this->service()->updateToken('6a1253d2-1785-24fd-91c2-f8e78c745510', [
            'Description'       =>  "Agent token for 'node1'",
            'Policies'          =>  [
                [
                    'ID'        =>  '165d4317-e379-f732-ce70-86278c4558f7',
                ],
                [
                    'Name'      =>  'node-read',
                ],
                [
                    'Name'      =>  'service-read',
                ],
            ],
            'Local'             =>  false,
        ]);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfCloneTokenReturnedValidData(): void
    {
        $parameters = [
            'Description'       =>  "Clone of Agent token for 'node1'",
        ];
        $response = $this->service()->cloneToken('6a1253d2-1785-24fd-91c2-f8e78c745511', $parameters);
        $this->assertEquals(AccessControlListHandler::getCreateTokenResponse($this->getEmulatedData('acl'), $parameters), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfCloneTokenFailedToUpdateSpecifiedToken(): void
    {
        $this->expectNotFound();
        $this->service()->cloneToken('6a1253d2-1785-24fd-91c2-f8e78c745510', [
            'Description'       =>  "Clone of Agent token for 'node1'",
        ]);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfTokenDeletionSucceeded(): void
    {
        $response = $this->service()->deleteToken('8f246b77-f3e1-ff88-5b48-8ec93abf3e05');
        $this->assertTrue($response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfCanListTokens(): void
    {
        $response = $this->service()->listTokens();
        $this->assertEquals(AccessControlListHandler::getListTokensResponse($this->getEmulatedData('acl')), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfPolicyCanBeCreatedWithValidData(): void
    {
        $parameters = [
            'Name'          => 'node-read',
            'Description'   => 'Grants read access to all node information',
            'Rules'         => 'node_prefix \'\' { policy = \'read\'}',
            'Datacenters'   => ['dc1'],
        ];

        $response = $this->service()->createPolicy($parameters);
        $this->assertEquals(AccessControlListHandler::getCreatePolicyResponse($this->getEmulatedData('acl'), $parameters), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfReadPolicyByIDReturnedValidData(): void
    {
        $response = $this->service()->readPolicy('e359bd81-baca-903e-7e64-1ccd9fdc78f5');
        $this->assertEquals(AccessControlListHandler::getReadPolicyResponse($this->getEmulatedData('acl')), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfReadPolicyByIDReturnedInvalidData(): void
    {
        $this->expectNotFound();
        $this->service()->readPolicy('e359bd81-baca-903e-7e64-1ccd9fdc78f4');
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfReadPolicyByNameReturnedValidData(): void
    {
        $response = $this->service()->readPolicyByName('node-read');
        $this->assertEquals(AccessControlListHandler::getReadPolicyResponse($this->getEmulatedData('acl')), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfUpdatePolicyReturnedValidData(): void
    {
        $parameters = [
            'Name'          =>  'register-app-service',
            'Description'   =>  "Grants write permissions necessary to register the 'app' service",
            'Rules'         =>  'service "app" { policy = "write" }',
        ];
        $response = $this->service()->updatePolicy('c01a1f82-44be-41b0-a686-685fb6e0f485', $parameters);
        $this->assertEquals(AccessControlListHandler::getCreatePolicyResponse($this->getEmulatedData('acl'), $parameters), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfFailedToUpdateSpecifiedPolicy(): void
    {
        $this->expectNotFound();
        $this->service()->updatePolicy('c01a1f82-44be-41b0-a686-685fb6e0f484', [
            'Name'          =>  'register-app-service',
            'Description'   =>  "Grants write permissions necessary to register the 'app' service",
            'Rules'         =>  'service "app" { policy = "write" }',
        ]);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfPolicyDeletionSucceeded(): void
    {
        $response = $this->service()->deletePolicy('8f246b77-f3e1-ff88-5b48-8ec93abf3e05');
        $this->assertTrue($response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfCanListPolicies(): void
    {
        $response = $this->service()->listPolicies();
        $this->assertEquals(AccessControlListHandler::getListPoliciesResponse($this->getEmulatedData('acl')), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfRoleCanBeCreatedWithValidData(): void
    {
        $parameters = [
            'Name' => 'example-role',
            'Description' => 'Showcases all input parameters',
            'Policies' => [
                [
                    'ID' => '783beef3-783f-f41f-7422-7087dc272765',
                ],
                [
                    'Name' => 'node-read',
                ],
            ],
            'ServiceIdentities' => [
                [
                    'ServiceName' => 'web',
                ],
                [
                    'ServiceName' => 'db',
                    'Datacenters' => ['dc1'],
                ],
            ],
            'NodeIdentities' => [
                [
                    'NodeName' => 'node-1',
                    'Datacenter' => 'dc2',
                ],
            ],
        ]
        ;

        $response = $this->service()->createRole($parameters);
        $this->assertEquals(AccessControlListHandler::getCreateRoleResponse($this->getEmulatedData('acl'), $parameters), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfReadRoleByIDReturnedValidData(): void
    {
        $response = $this->service()->readRole('aa770e5b-8b0b-7fcf-e5a1-8535fcc388b4');
        $this->assertEquals(AccessControlListHandler::getReadRoleResponse($this->getEmulatedData('acl')), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfReadRoleByIDReturnedInvalidData(): void
    {
        $this->expectNotFound();
        $this->service()->readRole('aa770e5b-8b0b-7fcf-e5a1-8535fcc388b3');
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfReadRoleByNameReturnedValidData(): void
    {
        $response = $this->service()->readRoleByName('example-role');
        $this->assertEquals(AccessControlListHandler::getReadRoleResponse($this->getEmulatedData('acl')), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfUpdateRoleReturnedValidData(): void
    {
        $parameters = [
            'Name' => 'example-two',
            'Policies' => [
                [
                    'Name' => 'node-read',
                ],
            ],
            'ServiceIdentities' => [
                [
                    'ServiceName' => 'db',
                ],
            ],
            'NodeIdentities' => [
                [
                    'NodeName' => 'node-1',
                    'Datacenter' => 'dc2',
                ],
            ],
        ];
        $response = $this->service()->updateRole('8bec74a4-5ced-45ed-9c9d-bca6153490bb', $parameters);
        $this->assertEquals(AccessControlListHandler::getCreateRoleResponse($this->getEmulatedData('acl'), $parameters), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfFailedToUpdateSpecifiedRole(): void
    {
        $this->expectNotFound();
        $this->service()->updateRole('8bec74a4-5ced-45ed-9c9d-bca6153490ba', [
            'Name' => 'example-two',
            'Policies' => [
                [
                    'Name' => 'node-read',
                ],
            ],
            'ServiceIdentities' => [
                [
                    'ServiceName' => 'db',
                ],
            ],
            'NodeIdentities' => [
                [
                    'NodeName' => 'node-1',
                    'Datacenter' => 'dc2',
                ],
            ],
        ]);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfRoleDeletionSucceeded(): void
    {
        $response = $this->service()->deleteRole('8f246b77-f3e1-ff88-5b48-8ec93abf3e05');
        $this->assertTrue($response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfCanListRoles(): void
    {
        $response = $this->service()->listRoles();
        $this->assertEquals(AccessControlListHandler::getListRolesResponse($this->getEmulatedData('acl')), $response);
    }

    /**
     * Create new instance of service
     * @return AccessControlListInterface
     */
    private function service(): AccessControlListInterface
    {
        Http::fake(function (Request $request): PromiseInterface {
            return (new AccessControlListHandler($request, $this->getEmulator('acl'), $this->getEmulatedData('acl')))->handle();
        });
        return new AccessControlList();
    }
}
