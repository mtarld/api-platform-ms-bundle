<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\ApiResource;

use Mtarld\ApiPlatformMsBundle\Exception\MicroserviceNotConfiguredException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

/**
 * @group resource-existence
 * @group http
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
        static::$container->get('api_platform_ms.api_resource.existence_checker')->getExistenceStatuses('foo', ['1']);
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
            ->expects(self::once())
            ->method('request')
            ->with(
                'POST',
                '/api/bar_check_resource',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'body' => '{"iris":["1"]}',
                ]
            )
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $httpClient);
        static::$container->get('api_platform_ms.api_resource.existence_checker')->getExistenceStatuses('bar', ['1']);
    }

    public function testParseSuccessfulMicroserviceResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('{"existences": {"1": true, "2": false}}'),
        ]);

        static::$container->set('test.http_client', $httpClient);
        $existences = static::$container->get('api_platform_ms.api_resource.existence_checker')->getExistenceStatuses('bar', ['1', '2']);
        self::assertEquals(['1' => true, '2' => false], $existences);
    }

    public function testErroredMicroserviceResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('', ['http_code' => 500]),
        ]);

        $this->expectException(Throwable::class);
        static::$container->set('test.http_client', $httpClient);
        static::$container->get('api_platform_ms.api_resource.existence_checker')->getExistenceStatuses('bar', ['1', '2']);
    }

    public function testParseWronglyFormattedMicroserviceResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('{"foo"}'),
        ]);

        $this->expectException(Throwable::class);
        static::$container->set('test.http_client', $httpClient);
        static::$container->get('api_platform_ms.api_resource.existence_checker')->getExistenceStatuses('bar', ['1', '2']);
    }

    public function testSwitchHttpClient(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn('{"existences":{"1": true}}')
        ;

        $firstHttpClient = $this->createMock(HttpClientInterface::class);
        $firstHttpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($response)
        ;

        $secondHttpClient = $this->createMock(HttpClientInterface::class);
        $secondHttpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $firstHttpClient);

        $existenceChecker = static::$container->get('api_platform_ms.api_resource.existence_checker');
        $existenceChecker->getExistenceStatuses('bar', ['1']);

        $existenceChecker->setHttpClient($secondHttpClient);
        $existenceChecker->getExistenceStatuses('bar', ['1']);
    }
}
