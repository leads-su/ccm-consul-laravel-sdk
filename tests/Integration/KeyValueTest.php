<?php

namespace Consul\Test\Integration;

use Consul\Test\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Consul\Services\KeyValue\KeyValue;
use Consul\Exceptions\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Consul\Testing\Services\KeyValueHandler;
use Consul\Services\KeyValue\Interfaces\KeyValueInterface;

/**
 * Class KeyValueTest
 *
 * @package Consul\Test\Integration
 */
class KeyValueTest extends TestCase
{
    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfValidDataIsReturnedFromGetKeyRequest(): void
    {
        $response = $this->service()->get('consul-laravel/example_string');
        $scriptedResponse = KeyValueHandler::getKeyResponseData($this->getEmulatedData('kv'), 'consul-laravel/example_string');
        $this->assertCount(count($scriptedResponse), $response);
        $this->assertEquals($scriptedResponse[0]['Value'], $response[0]['Value']);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfValidDataIsReturnedFromGetKeysRequest(): void
    {
        $response = $this->service()->get('test', ['keys']);
        $scriptedResponse = KeyValueHandler::getKeysResponseData($this->getEmulatedData('kv'), 'test');
        $this->assertEquals($scriptedResponse, $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfValidDataIsReturnedFromGetRecurseRequest(): void
    {
        $response = array_map(static function (array $item): array {
            return Arr::except($item, ['CreateIndex', 'ModifyIndex']);
        }, $this->service()->get('test', ['recurse']));

        $scriptedResponse = array_map(static function (array $item): array {
            return Arr::except($item, ['CreateIndex', 'ModifyIndex']);
        }, KeyValueHandler::getRecurseResponseData($this->getEmulatedData('kv'), 'test'));

        $this->assertEquals($scriptedResponse, $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfValidDataIsReturnedFromGetRawRequest(): void
    {
        $response = $this->service()->get('consul-laravel/example_string', ['raw']);
        $this->assertEquals(json_decode(
            KeyValueHandler::getRawResponseData($this->getEmulatedData('kv'), 'consul-laravel/example_string'),
            true,
        ), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfValidDataIsReturnedFromGetRecurseRawResponse(): void
    {
        $response = array_map(static function (array $item): array {
            return Arr::except($item, ['CreateIndex', 'ModifyIndex']);
        }, $this->service()->get('test', ['recurse', 'raw']));

        $scriptedResponse = array_map(static function (array $item): array {
            return Arr::except($item, ['CreateIndex', 'ModifyIndex']);
        }, KeyValueHandler::getRecurseResponseData($this->getEmulatedData('kv'), 'test'));

        $this->assertEquals($scriptedResponse, $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfValidDataIsReturnedFromGetRecurseKeysResponse(): void
    {
        $response = $this->service()->get('test', ['recurse', 'keys']);
        $this->assertEquals(KeyValueHandler::getKeysResponseData($this->getEmulatedData('kv'), 'test'), $response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfEmptyKeyIsPassedToGetRequest(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Bad Request');
        $this->service()->get('');
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfNonExistentKeyIsRequestedForGetRequest(): void
    {
        $this->expectNotFound();
        $this->service()->get('missing');
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfInvalidDatacenterSpecifiedForGetRequest(): void
    {
        $this->expectInternalServerError();
        $this->service()->get('test', ['dc' => 'invalid_datacenter']);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfEmptyKeyIsPassedToPutRequest(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Bad Request');
        $this->service()->put('', 'value');
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfValidDataProvidedToPutRequest(): void
    {
        $response = $this->service()->put('new_key', json_encode(['type' => 'string', 'value' => 'new key value']));
        $this->assertTrue($response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfInvalidDataProvidedToPutRequest(): void
    {
        $this->expectException(RequestException::class);
        $this->service()->put('fail', 'any_value', ['dc' => '123']);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfEmptyKeyIsPassedToDeleteRequest(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Bad Request');
        $this->service()->delete('');
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldPassIfValidDataProvidedToDeleteRequest(): void
    {
        $response = $this->service()->delete('new_key');
        $this->assertTrue($response);
    }

    /**
     * @throws RequestException
     * @return void
     */
    public function testShouldFailIfInvalidDataProvidedToDeleteRequest(): void
    {
        $response = $this->service()->delete('non_existent_key');
        $this->assertTrue($response);
    }

    /**
     * Create new instance of service
     * @return KeyValueInterface
     */
    private function service(): KeyValueInterface
    {
        if (config('consul.emulate', false)) {
            Http::fake(function (Request $request): PromiseInterface {
                return (new KeyValueHandler($request, $this->getEmulator('kv'), $this->getEmulatedData('kv')))->handle();
            });
        }
        return new KeyValue();
    }
}
