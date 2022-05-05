<?php

namespace Consul\Services\AccessControlList\Interfaces;

use Consul\Exceptions\RequestException;

/**
 * Interface AccessControlListInterface
 *
 * @package Consul\Services\AccessControlList\Interfaces
 */
interface AccessControlListInterface
{
    /**
     * This endpoint does a special one-time bootstrap of the ACL system, making the first management token if the
     * acl.tokens.master configuration entry is not specified in the Consul server configuration and if the cluster has
     * not been bootstrapped previously. This is available in Consul 0.9.1 and later, and requires all Consul servers
     * to be upgraded in order to operate.
     *
     * @throws RequestException
     * @return array
     */
    public function bootstrap(): array;

    /**
     * This endpoint returns the status of the ACL replication processes in the datacenter.
     * This is intended to be used by operators or by automation checking to discover the health of ACL replication.
     *
     * @param string $datacenter Specifies the datacenter to query.
     *
     * @return array
     * @throws RequestException
     */
    public function replicationStatus(string $datacenter = 'dc0'): array;

    /**
     * This endpoint translates the legacy rule syntax into the latest syntax.
     * It is intended to be used by operators managing Consul's ACLs and performing legacy token to new policy migrations.
     *
     * @param string $rule
     *
     * @throws RequestException
     *@return string
     */
    public function translateRule(string $rule): string;

    /**
     * This endpoint translates the legacy rules embedded within a legacy ACL into the latest syntax.
     * It is intended to be used by operators managing Consul's ACLs and performing legacy token to new policy migrations.
     * Note that this API requires the auto-generated Accessor ID of the legacy token.
     * This ID can be retrieved using the /v1/acl/token/self endpoint.
     *
     * @param string $accessorID
     *
     * @throws RequestException
     * @return string
     */
    public function translateLegacyRule(string $accessorID): string;

    /**
     * This endpoint creates a new ACL token.
     * @param array $options
     *
     * @throws RequestException
     * @return array
     */
    public function createToken(array $options = []): array;

    /**
     * This endpoint reads an ACL token with the given Accessor ID.
     * @param string $accessorID
     *
     * @throws RequestException
     * @return array
     */
    public function readToken(string $accessorID): array;

    /**
     * This endpoint returns the ACL token details that matches the secret ID specified with the X-Consul-Token
     * header or the token query parameter.
     *
     * @throws RequestException
     * @return array
     */
    public function myToken(): array;

    /**
     * This endpoint updates an existing ACL token.
     * @param string $accessorID
     * @param array  $options
     *
     * @throws RequestException
     * @return array
     */
    public function updateToken(string $accessorID, array $options = []): array;

    /**
     * This endpoint clones an existing ACL token.
     * @param string $accessorID
     * @param array  $options
     *
     * @throws RequestException
     * @return array
     */
    public function cloneToken(string $accessorID, array $options = []): array;

    /**
     * This endpoint deletes an ACL token.
     * @param string $accessorID
     *
     * @throws RequestException
     * @return bool
     */
    public function deleteToken(string $accessorID): bool;

    /**
     * This endpoint lists all the ACL tokens.
     *
     * @param array $options
     *
     * @throws RequestException
     * @return array
     */
    public function listTokens(array $options = []): array;

    /**
     * This endpoint creates a new ACL policy.
     * @param array $options
     *
     * @return array
     * @throws RequestException
     */
    public function createPolicy(array $options = []): array;

    /**
     * This endpoint reads an ACL policy with the given ID.
     * @param string $accessorID
     *
     * @return array
     * @throws RequestException
     */
    public function readPolicy(string $accessorID): array;

    /**
     * This endpoint reads an ACL policy with the given Name.
     * @param string $policyName
     *
     * @return array
     * @throws RequestException
     */
    public function readPolicyByName(string $policyName): array;

    /**
     * This endpoint updates an existing ACL policy.
     * @param string $accessorID
     * @param array  $options
     *
     * @return array
     * @throws RequestException
     */
    public function updatePolicy(string $accessorID, array $options = []): array;

    /**
     * This endpoint deletes an ACL policy.
     * @param string $accessorID
     *
     * @return bool
     * @throws RequestException
     */
    public function deletePolicy(string $accessorID): bool;

    /**
     * This endpoint lists all the ACL policies.
     * @return array
     * @throws RequestException
     */
    public function listPolicies(): array;

    /**
     * @param array $options
     *
     * @return array
     * @throws RequestException
     */
    public function createRole(array $options = []): array;

    /**
     * @param string $accessorID
     *
     * @return array
     * @throws RequestException
     */
    public function readRole(string $accessorID): array;

    /**
     * @param string $roleName
     *
     * @return array
     * @throws RequestException
     */
    public function readRoleByName(string $roleName): array;

    /**
     * @param string $accessorID
     * @param array  $options
     *
     * @return array
     * @throws RequestException
     */
    public function updateRole(string $accessorID, array $options = []): array;

    /**
     * @param string $accessorID
     *
     * @return bool
     * @throws RequestException
     */
    public function deleteRole(string $accessorID): bool;

    /**
     * @param array $options
     *
     * @return array
     * @throws RequestException
     */
    public function listRoles(array $options = []): array;
}
