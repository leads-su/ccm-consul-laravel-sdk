<?php

namespace Consul\Test\Integration;

use Consul\Test\TestCase;
use Illuminate\Support\Arr;
use Consul\Services\Agent\Agent;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Consul\Exceptions\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Consul\Testing\Services\AgentHandler;
use Consul\Services\Agent\Interfaces\AgentInterface;

/**
 * Class AgentTest
 *
 * @package Consul\Test\Integration
 */
class AgentTest extends TestCase
{
    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::host()
     */
    public function testShouldPassIfValidDataReturnedFromHostRequest(): void
    {
        $response = $this->service()->host();
        $scriptedResponse = AgentHandler::getHostResponse($this->emulatedData());

        foreach (array_keys($scriptedResponse) as $key) {
            $this->assertArrayHasKey($key, $response);
        }
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::members()
     */
    public function testShouldPassIfValidDataReturnedFromMembersRequest(): void
    {
        $response = $this->service()->members();
        $scriptedResponse = AgentHandler::getMembersResponse($this->emulatedData());

        foreach (array_keys($scriptedResponse) as $key) {
            $this->assertArrayHasKey($key, $response);
        }
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::self()
     */
    public function testShouldPassIfValidDataReturnedFromSelfRequest(): void
    {
        $response = $this->service()->self();
        $scriptedResponse = AgentHandler::getSelfResponse($this->emulatedData());

        foreach (array_keys($scriptedResponse) as $key) {
            $this->assertArrayHasKey($key, $response);
        }
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::metrics()
     */
    public function testShouldPassIfValidDataReturnedFromMetricsRequest(): void
    {
        $response = $this->service()->metrics();
        $scriptedResponse = AgentHandler::getMetricsResponse($this->emulatedData());

        foreach (array_keys($scriptedResponse) as $key) {
            $this->assertArrayHasKey($key, $response);
        }
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::streamLogs()
     */
    public function testShouldPassIfValidDataReturnedFromMonitorRequest(): void
    {
        $this->assertTrue(true);
        // TODO: Figure out how to test it without hanging whole test suite
//        $response = $this->service()->streamLogs();
//        $this->assertEquals(AgentHandler::getMonitorResponse($this->emulatedData()), $response);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::registerCheck()
     */
    public function testShouldPassIfValidDataReturnedFromRegisterCheckRequest(): void
    {
        $response = $this->service()->registerCheck([
            'ID'                                =>  "ping_google",
            'Name'                              =>  "Check if Google is reachable",
            'Notes'                             =>  "Ensure that we are able to reach google",
            'DeregisterCriticalServiceAfter'    =>  "90m",
            'Timeout'                           =>  '5s',
            'TLSSkipVerify'                     =>  true,
            'TTL'                               =>  '30s',
        ]);
        $this->assertEquals([], $response);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::registerCheck()
     */
    public function testShouldFailIfInvalidDataReturnedFromRegisterCheckRequest(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('Bad Request');
        $this->service()->registerCheck([
            'Name'                              =>  "Memory utilization",
        ]);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::listChecks()
     */
    public function testShouldPassIfValidDataReturnedFromListChecksRequest(): void
    {
        $response = $this->service()->listChecks();
        $this->assertEquals(AgentHandler::getListChecksResponse($this->emulatedData()), $response);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::ttlCheckPass()
     */
    public function testShouldPassIfValidDataReturnedFromTtlCheckPassRequest(): void
    {
        $response = $this->service()->ttlCheckPass('ping_google');
        $this->assertEquals([], $response);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::ttlCheckPass()
     */
    public function testShouldFailIfInvalidDataReturnedFromTtlCheckPassRequest(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionCode(500);
        $this->service()->ttlCheckPass('check_google');
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::ttlCheckFail()
     */
    public function testShouldPassIfValidDataReturnedFromTtlCheckFailRequest(): void
    {
        $response = $this->service()->ttlCheckFail('ping_google');
        $this->assertEquals([], $response);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::ttlCheckFail()
     */
    public function testShouldFailIfInvalidDataReturnedFromTtlCheckFailRequest(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionCode(500);
        $this->service()->ttlCheckFail('check_google');
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::ttlCheckWarn()
     */
    public function testShouldPassIfValidDataReturnedFromTtlCheckWarnRequest(): void
    {
        $response = $this->service()->ttlCheckWarn('ping_google');
        $this->assertEquals([], $response);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::ttlCheckWarn()
     */
    public function testShouldFailIfInvalidDataReturnedFromTtlCheckWarnRequest(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionCode(500);
        $this->service()->ttlCheckWarn('check_google');
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::deRegisterCheck()
     */
    public function testShouldPassIfValidDataReturnedFromDeregisterCheckRequest(): void
    {
        $response = $this->service()->deRegisterCheck('ping_google');
        $this->assertEquals([], $response);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::registerService()
     */
    public function testShouldPassIfValidDataReturnedFromRegisterServiceRequest(): void
    {
        $response = $this->service()->registerService([
            'ID'            =>  'redis1',
            'Name'          =>  'redis',
            'Tags'          =>  ['primary', 'v1'],
            'Address'       =>  '127.0.0.1',
            'Port'          =>  8000,
            'Weights'       =>  [
                'Passing'   =>  10,
                'Warning'   =>  1,
            ],
        ]);
        $this->assertEquals([], $response);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::listServices()
     */
    public function testShouldPassIfValidDataReturnedFromListServicesRequest(): void
    {
        $response = $this->service()->listServices();
        $this->assertEquals(AgentHandler::getListServicesResponse($this->emulatedData()), $response);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::serviceConfiguration()
     */
    public function testShouldPassIfValidDataReturnedFromServiceConfigurationRequest(): void
    {
        $response = Arr::except($this->service()->serviceConfiguration('redis1'), [
            'ContentHash',
        ]);

        $scriptedResponse = Arr::except(AgentHandler::getServiceConfigurationResponse($this->emulatedData()), [
            'ContentHash',
        ]);

        $this->assertEquals($scriptedResponse, $response);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::localServiceHealth()
     */
    public function testShouldPassIfValidDataReturnedFromGetServiceHealthByNameRequest(): void
    {
        $response = $this->service()->localServiceHealth('redis');
        $this->assertEquals(AgentHandler::getServiceHealthResponse($this->emulatedData()), $response);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::localServiceHealthByID()
     */
    public function testShouldPassIfValidDataReturnedFromGetServiceHealthByIDRequest(): void
    {
        $response = $this->service()->localServiceHealthByID('redis1');
        $this->assertEquals(Arr::first(AgentHandler::getServiceHealthResponse($this->emulatedData())), $response);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::toggleMaintenanceMode()
     */
    public function testShouldPassIfValidDataReturnedFromServiceMaintenanceModeEnableRequest(): void
    {
        $response = $this->service()->toggleMaintenanceMode('redis1', true, 'Example Reason');
        $this->assertEquals([], $response);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::toggleMaintenanceMode()
     */
    public function testShouldPassIfValidDataReturnedFromServiceMaintenanceModeDisableRequest(): void
    {
        $response = $this->service()->toggleMaintenanceMode('redis1', false, 'Example Reason');
        $this->assertEquals([], $response);
    }

    /**
     * @throws RequestException
     * @return void
     * @see \Consul\Services\Agent\Agent::deRegisterService()
     */
    public function testShouldPassIfValidDataReturnedFromDeregisterServiceRequest(): void
    {
        $response = $this->service()->deRegisterService('redis1');
        $this->assertEquals([], $response);
    }

    /**
     * Create new instance of service
     * @return AgentInterface
     */
    private function service(): AgentInterface
    {
        if (config('consul.emulate', false)) {
            Http::fake(function (Request $request): PromiseInterface {
                return (new AgentHandler($request, $this->getEmulator('agent'), $this->getEmulatedData('agent')))->handle();
            });
        }
        return new Agent();
    }

    /**
     * Get emulated data for test
     * @return mixed
     */
    private function emulatedData(): mixed
    {
        return $this->getEmulatedData('agent');
    }
}
