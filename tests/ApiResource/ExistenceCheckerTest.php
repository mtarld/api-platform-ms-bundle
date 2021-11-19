<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\ApiResource;

use Mtarld\ApiPlatformMsBundle\Tests\BcLayer\BcLayerKernelTestCase;
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
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ExistenceCheckerTest extends BcLayerKernelTestCase
{
    protected function setUp(): void
    {
        static::bootKernel();
    }

    public function testMicroserviceNotConfigured(): void
    {
        $this->expectException(MicroserviceNotConfiguredException::class);
        static::getContainer()->get('api_platform_ms.api_resource.existence_checker')->getExistenceStatuses('foo', ['1']);
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

        static::getContainer()->set('test.http_client', $httpClient);
        static::getContainer()->get('api_platform_ms.api_resource.existence_checker')->getExistenceStatuses('bar', ['1']);
    }

    public function testParseSuccessfulMicroserviceResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('{"existences": {"1": true, "2": false}}'),
        ]);

        static::getContainer()->set('test.http_client', $httpClient);
        $existences = static::getContainer()->get('api_platform_ms.api_resource.existence_checker')->getExistenceStatuses('bar', ['1', '2']);
        self::assertEquals(['1' => true, '2' => false], $existences);
    }

    public function testErroredMicroserviceResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('', ['http_code' => 500]),
        ]);

        $this->expectException(Throwable::class);
        static::getContainer()->set('test.http_client', $httpClient);
        static::getContainer()->get('api_platform_ms.api_resource.existence_checker')->getExistenceStatuses('bar', ['1', '2']);
    }

    public function testParseWronglyFormattedMicroserviceResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('{"foo"}'),
        ]);

        $this->expectException(Throwable::class);
        static::getContainer()->set('test.http_client', $httpClient);
        static::getContainer()->get('api_platform_ms.api_resource.existence_checker')->getExistenceStatuses('bar', ['1', '2']);
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

        static::getContainer()->set('test.http_client', $firstHttpClient);

        $existenceChecker = static::getContainer()->get('api_platform_ms.api_resource.existence_checker');
        $existenceChecker->getExistenceStatuses('bar', ['1']);

        $existenceChecker->setWrappedHttpClient($secondHttpClient);
        $existenceChecker->getExistenceStatuses('bar', ['1']);
    }
}
