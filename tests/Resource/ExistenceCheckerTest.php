<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Resource;

use Mtarld\ApiPlatformMsBundle\Exception\MicroserviceNotConfiguredException;
use Mtarld\ApiPlatformMsBundle\Resource\ExistenceChecker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

/**
 * @group functional
 * @group resource-existence
 */
class ExistenceCheckerTest extends KernelTestCase
{
    protected function setUp(): void
    {
        static::bootKernel();
    }

    public function testMicroserviceNotConfigured(): void
    {
        $this->expectException(MicroserviceNotConfiguredException::class);
        static::$container->get(ExistenceChecker::class)->getExistenceStatuses('foo', ['1']);
    }

    public function testCallOtherMicroservice(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn('{"existences":{"1": true}}')
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '/bar_check_resource',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept: application/json',
                    ],
                    'body' => '{"iris":["1"]}',
                ]
            )
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $httpClient);
        static::$container->get(ExistenceChecker::class)->getExistenceStatuses('bar', ['1']);
    }

    public function testParseSuccessfulMicroserviceResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('{"existences": {"1": true, "2": false}}'),
        ]);

        static::$container->set('test.http_client', $httpClient);
        $existences = static::$container->get(ExistenceChecker::class)->getExistenceStatuses('bar', ['1', '2']);
        $this->assertEquals(['1' => true, '2' => false], $existences);
    }

    public function testErroredMicroserviceResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('', ['http_code' => 500]),
        ]);

        $this->expectException(Throwable::class);
        static::$container->set('test.http_client', $httpClient);
        static::$container->get(ExistenceChecker::class)->getExistenceStatuses('bar', ['1', '2']);
    }

    public function testParseWronglyFormattedMicroserviceResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('{"foo"}'),
        ]);

        $this->expectException(Throwable::class);
        static::$container->set('test.http_client', $httpClient);
        static::$container->get(ExistenceChecker::class)->getExistenceStatuses('bar', ['1', '2']);
    }
}
