<?php

namespace Consul\Services\Agent;

use Consul\Abstracts\AbstractService;
use Consul\Services\Agent\Interfaces\AgentInterface;

/**
 * Class Agent
 *
 * @package Consul\Services\Agent
 */
class Agent extends AbstractService implements AgentInterface
{
    /**
     * @inheritDoc
     */
    public function host(): array
    {
        return $this->processGetRequest('agent/host');
    }

    /**
     * @inheritDoc
     */
    public function members(bool $wan = false): array
    {
        return $this->processGetRequest('agent/members', [
            'wan'   =>  $wan,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function self(): array
    {
        return $this->processGetRequest('agent/self');
    }

    /**
     * @inheritDoc
     */
    public function metrics(bool $forPrometheus = false): array
    {
        return $this->processGetRequest('agent/metrics');
    }

    /**
     * @inheritDoc
     */
    public function streamLogs(string $logLevel = 'info', bool $logJson = false): mixed
    {
        // @codeCoverageIgnoreStart
        return $this->processGetRequest('agent/monitor', [
            'loglevel'  =>  $logLevel,
            'logjson'   =>  $logJson,
        ]);
        // @codeCoverageIgnoreEnd
    }

    /**
     * @inheritDoc
     */
    public function listChecks(string $filter = ''): array
    {
        return $this->processGetRequest('agent/checks', [
            'filter'    =>  $filter,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function registerCheck(array $options = []): array
    {
        return $this->processPutRequest('agent/check/register', $this->resolveOptions($options, [
            'ID', 'Name', 'Namespace', 'Interval', 'Notes',
            'DeregisterCriticalServiceAfter', 'Args', 'AliasNode',
            'AliasService', 'DockerContainerID', 'GRPC', 'GRPCUseTLS',
            'H2PING', 'HTTP', 'Method', 'Body', 'Header', 'Timeout',
            'OutputMaxSize', 'TLSServerName', 'TLSSkipVerify', 'TCP',
            'TTL', 'ServiceID', 'Status', 'SuccessBeforePassing',
            'FailuresBeforeCritical',
        ]), []);
    }

    /**
     * @inheritDoc
     */
    public function deRegisterCheck(string $checkID): array
    {
        return $this->processPutRequest(sprintf('agent/check/deregister/%s', $checkID));
    }

    /**
     * @inheritDoc
     */
    public function ttlCheckPass(string $checkID, string $note = ''): array
    {
        return $this->processPutRequest(sprintf('agent/check/pass/%s', $checkID), null, [
            'note'  =>  $note,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function ttlCheckWarn(string $checkID, string $note = ''): array
    {
        return $this->processPutRequest(sprintf('agent/check/warn/%s', $checkID), null, [
            'note'  =>  $note,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function ttlCheckFail(string $checkID, string $note = ''): array
    {
        return $this->processPutRequest(sprintf('agent/check/fail/%s', $checkID), null, [
            'note'  =>  $note,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function listServices(string $filter = ''): array
    {
        return $this->processGetRequest('agent/services', [
            'filter'    =>  $filter,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function serviceConfiguration(string $serviceID): array
    {
        return $this->processGetRequest(sprintf('agent/service/%s', $serviceID));
    }

    /**
     * @inheritDoc
     */
    public function localServiceHealth(string $serviceName): array
    {
        return $this->processGetRequest(sprintf('agent/health/service/name/%s', $serviceName));
    }

    /**
     * @inheritDoc
     */
    public function localServiceHealthByID(string $serviceID): array
    {
        return $this->processGetRequest(sprintf('agent/health/service/id/%s', $serviceID));
    }

    /**
     * @inheritDoc
     */
    public function registerService(array $options = []): array
    {
        return $this->processPutRequest('agent/service/register', $this->resolveOptions($options, [
            'ID', 'Name', 'Tags', 'Address', 'TaggedAddresses', 'Meta', 'Port',
            'Kind', 'Proxy', 'Connect', 'Check', 'Checks', 'EnableTagOverride',
            'Weights',
        ]), []);
    }

    /**
     * @inheritDoc
     */
    public function deRegisterService(string $serviceID): array
    {
        return $this->processPutRequest(sprintf('agent/service/deregister/%s', $serviceID));
    }

    /**
     * @inheritDoc
     */
    public function toggleMaintenanceMode(string $serviceID, bool $enabled, string $reason = ''): array
    {
        return $this->processPutRequest(sprintf('agent/service/maintenance/%s', $serviceID), null, [
            'enable'        =>  $enabled ? 'true' : 'false',
            'reason'        =>  $reason,
        ]);
    }
}
