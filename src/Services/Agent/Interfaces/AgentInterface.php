<?php

namespace Consul\Services\Agent\Interfaces;

use Consul\Exceptions\RequestException;

/**
 * Interface AgentInterface
 *
 * @package Consul\Services\Agent\Interfaces
 */
interface AgentInterface
{
    /**
     * This endpoint returns information about the host the agent is running on such as CPU, memory, and disk.
     *
     * @throws RequestException
     * @return array
     */
    public function host(): array;

    /**
     * This endpoint returns the members the agent sees in the cluster gossip pool.
     * Due to the nature of gossip, this is eventually consistent: the results may differ by agent.
     *
     * @param bool $wan
     *
     * @throws RequestException
     * @return array
     */
    public function members(bool $wan = false): array;

    /**
     * This endpoint returns the configuration and member information of the local agent.
     *
     * he Config element contains a subset of the configuration and its format will not change in a backwards
     * incompatible way between releases. DebugConfig contains the full runtime configuration but its format is subject
     * to change without notice or deprecation.
     *
     * @throws RequestException
     * @return array
     */
    public function self(): array;

    /**
     * This endpoint will dump the metrics for the most recent finished interval.
     * For more information about metrics, see the telemetry page.
     *
     * @throws RequestException
     * @return array
     */
    public function metrics(): array;

    /**
     * This endpoint streams logs from the local agent until the connection is closed.
     * @param string $logLevel
     * @param bool   $logJson
     *
     * @throws RequestException
     * @return mixed
     */
    public function streamLogs(string $logLevel = 'info', bool $logJson = false): mixed;

    /**
     * This endpoint returns all checks that are registered with the local agent.
     * These checks were either provided through configuration files or added dynamically using the HTTP API.
     *
     * @param string $filter
     *
     * @throws RequestException
     * @return array
     */
    public function listChecks(string $filter = ''): array;

    /**
     * This endpoint adds a new check to the local agent.
     * Checks may be of script, HTTP, TCP, or TTL type.
     * The agent is responsible for managing the status of the check and keeping the Catalog in sync.
     *
     * @param array $options
     *
     * @throws RequestException
     * @return array
     */
    public function registerCheck(array $options = []): array;

    /**
     * This endpoint remove a check from the local agent.
     * The agent will take care of deregistering the check from the catalog.
     * If the check with the provided ID does not exist, no action is taken.
     *
     * @param string $checkID
     *
     * @throws RequestException
     * @return array
     */
    public function deRegisterCheck(string $checkID): array;

    /**
     * This endpoint is used with a TTL type check to set the status of the check to passing and to reset the TTL clock.
     * @param string $checkID
     * @param string $note
     *
     * @throws RequestException
     * @return array
     */
    public function ttlCheckPass(string $checkID, string $note = ''): array;

    /**
     * This endpoint is used with a TTL type check to set the status of the check to warning and to reset the TTL clock.
     * @param string $checkID
     * @param string $note
     *
     * @throws RequestException
     * @return array
     */
    public function ttlCheckWarn(string $checkID, string $note = ''): array;

    /**
     * This endpoint is used with a TTL type check to set the status of the check to critical and to reset the TTL clock.
     * @param string $checkID
     * @param string $note
     *
     * @throws RequestException
     * @return array
     */
    public function ttlCheckFail(string $checkID, string $note = ''): array;

    /**
     * This endpoint returns all the services that are registered with the local agent.
     * These services were either provided through configuration files or added dynamically using the HTTP API.
     *
     * @param string $filter
     *
     * @throws RequestException
     * @return array
     */
    public function listServices(string $filter = ''): array;

    /**
     * This endpoint returns the full service definition for a single service instance registered on the local agent.
     * It is used by Connect proxies to discover the embedded proxy configuration that was registered with the instance.
     *
     * @param string $serviceID
     *
     * @throws RequestException
     * @return array
     */
    public function serviceConfiguration(string $serviceID): array;

    /**
     * Retrieve an aggregated state of service(s) on the local agent by name.
     * @param string $serviceName
     *
     * @throws RequestException
     * @return array
     */
    public function localServiceHealth(string $serviceName): array;

    /**
     * Retrieve the health state of a specific service on the local agent by ID.
     * @param string $serviceID
     *
     * @throws RequestException
     * @return array
     */
    public function localServiceHealthByID(string $serviceID): array;

    /**
     * This endpoint adds a new service, with optional health checks, to the local agent.
     *
     * The agent is responsible for managing the status of its local services, and for sending updates about its
     * local services to the servers to keep the global catalog in sync.
     *
     * For "connect-proxy" kind services, the service:write ACL for the Proxy.
     * DestinationServiceName value is also required to register the service.
     *
     * @param array $options
     *
     * @throws RequestException
     * @return array
     */
    public function registerService(array $options = []): array;

    /**
     * This endpoint removes a service from the local agent.
     * If the service does not exist, no action is taken.
     *
     * The agent will take care of deregistering the service with the catalog.
     * If there is an associated check, that is also deregistered.
     *
     * @param string $serviceID
     *
     * @throws RequestException
     * @return array
     */
    public function deRegisterService(string $serviceID): array;

    /**
     * This endpoint places a given service into "maintenance mode".
     * During maintenance mode, the service will be marked as unavailable and will not be present in DNS or API queries.
     * This API call is idempotent.
     * Maintenance mode is persistent and will be automatically restored on agent restart.
     *
     * @param string $serviceID
     * @param bool   $enabled
     * @param string $reason
     *
     * @throws RequestException
     * @return array
     */
    public function toggleMaintenanceMode(string $serviceID, bool $enabled, string $reason = ''): array;
}
