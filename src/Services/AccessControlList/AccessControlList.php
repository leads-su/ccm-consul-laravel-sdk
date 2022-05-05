<?php

namespace Consul\Services\AccessControlList;

use Consul\Abstracts\AbstractService;
use Consul\Services\AccessControlList\Interfaces\AccessControlListInterface;

/**
 * Class AccessControlList
 *
 * @package Consul\Services\AccessControlList
 */
class AccessControlList extends AbstractService implements AccessControlListInterface
{
    /**
     * @inheritDoc
     */
    public function bootstrap(): array
    {
        return $this->processPutRequest('acl/bootstrap', []);
    }

    /**
     * @inheritDoc
     */
    public function replicationStatus(string $datacenter = 'dc0'): array
    {
        return $this->processGetRequest('acl/replication', $this->resolveOptions([
            'dc'    =>  $datacenter,
        ], [
            'dc',
        ]));
    }

    /**
     * @inheritDoc
     */
    public function translateRule(string $rule): string
    {
        return $this->processPostRequest('acl/rules/translate', $rule);
    }

    /**
     * @inheritDoc
     */
    public function translateLegacyRule(string $accessorID): string
    {
        return $this->processPostRequest(sprintf('acl/rules/translate/%s', $accessorID), null);
    }

    /**
     * @inheritDoc
     */
    public function createToken(array $options = []): array
    {
        return $this->processPutRequest('acl/token', $this->resolveOptions($options, [
            'AccessorID', 'SecretID', 'Description', 'Policies', 'Roles',
            'ServiceIdentities', 'NodeIdentities', 'Local', 'ExpirationTime',
            'ExpirationTTL', 'Namespace',
        ]));
    }

    /**
     * @inheritDoc
     */
    public function readToken(string $accessorID): array
    {
        return $this->processGetRequest(sprintf('acl/token/%s', $accessorID));
    }

    /**
     * @inheritDoc
     */
    public function myToken(): array
    {
        return $this->processGetRequest('acl/token/self');
    }

    /**
     * @inheritDoc
     */
    public function updateToken(string $accessorID, array $options = []): array
    {
        return $this->processPutRequest(sprintf('acl/token/%s', $accessorID), $this->resolveOptions($options, [
            'AccessorID', 'SecretID', 'Description', 'Policies', 'Roles',
            'ServiceIdentities', 'NodeIdentities', 'Local', 'ExpirationTime',
            'ExpirationTTL', 'Namespace',
        ]));
    }

    /**
     * @inheritDoc
     */
    public function cloneToken(string $accessorID, array $options = []): array
    {
        return $this->processPutRequest(sprintf('acl/token/%s/clone', $accessorID), null, $this->resolveOptions($options, [
            'AccessorID', 'SecretID', 'Description', 'Namespace',
        ]));
    }

    /**
     * @inheritDoc
     */
    public function deleteToken(string $accessorID): bool
    {
        return $this->processDeleteRequest(sprintf('acl/token/%s', $accessorID), []);
    }

    /**
     * @inheritDoc
     */
    public function listTokens(array $options = []): array
    {
        return $this->processGetRequest('acl/tokens', $this->resolveOptions($options, [
            'policy', 'role', 'authmethod',
            'authmethod-ns', 'ns',
        ]));
    }

    /**
     * @inheritDoc
     */
    public function createPolicy(array $options = []): array
    {
        return $this->processPutRequest('acl/policy', $this->resolveOptions($options, [
            'Name', 'Description', 'Rules', 'Datacenters', 'Namespace',
        ]));
    }

    /**
     * @inheritDoc
     */
    public function readPolicy(string $accessorID): array
    {
        return $this->processGetRequest(sprintf('acl/policy/%s', $accessorID));
    }

    /**
     * @inheritDoc
     */
    public function readPolicyByName(string $policyName): array
    {
        return $this->processGetRequest(sprintf('acl/policy/name/%s', $policyName));
    }

    /**
     * @inheritDoc
     */
    public function updatePolicy(string $accessorID, array $options = []): array
    {
        return $this->processPutRequest(sprintf('acl/policy/%s', $accessorID), $this->resolveOptions($options, [
            'Name', 'Description', 'Rules', 'Datacenters', 'Namespace',
        ]));
    }

    /**
     * @inheritDoc
     */
    public function deletePolicy(string $accessorID): bool
    {
        return $this->processDeleteRequest(sprintf('acl/policy/%s', $accessorID));
    }

    /**
     * @inheritDoc
     */
    public function listPolicies(): array
    {
        return $this->processGetRequest('acl/policies');
    }

    /**
     * @inheritDoc
     */
    public function createRole(array $options = []): array
    {
        return $this->processPutRequest('acl/role', $this->resolveOptions($options, [
            'Name', 'Description', 'Policies', 'ServiceIdentities', 'NodeIdentities',
            'Namespace',
        ]));
    }

    /**
     * @inheritDoc
     */
    public function readRole(string $accessorID): array
    {
        return $this->processGetRequest(sprintf('acl/role/%s', $accessorID));
    }

    /**
     * @inheritDoc
     */
    public function readRoleByName(string $roleName): array
    {
        return $this->processGetRequest(sprintf('acl/role/name/%s', $roleName));
    }

    /**
     * @inheritDoc
     */
    public function updateRole(string $accessorID, array $options = []): array
    {
        return $this->processPutRequest(sprintf('acl/role/%s', $accessorID), $this->resolveOptions($options, [
            'Name', 'Description', 'Policies', 'ServiceIdentities', 'NodeIdentities', 'Namespace',
        ]));
    }

    /**
     * @inheritDoc
     */
    public function deleteRole(string $accessorID): bool
    {
        return $this->processDeleteRequest(sprintf('acl/role/%s', $accessorID));
    }

    /**
     * @inheritDoc
     */
    public function listRoles(array $options = []): array
    {
        return $this->processGetRequest('acl/roles', $this->resolveOptions($options, [
            'policy',
        ]));
    }
}
