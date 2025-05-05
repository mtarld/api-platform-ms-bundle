<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\ApiResource;

use Mtarld\ApiPlatformMsBundle\Exception\MicroserviceNotConfiguredException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @group resource-existence
 * @group http
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
final class ExistenceVerifierTest extends KernelTestCase
{
    public function testMicroserviceNotConfigured(): void
    {
        $this->expectException(MicroserviceNotConfiguredException::class);

        self::getContainer()->get('test.existence_verifier')->verify('foo', '/api/products/1');
    }

    public function testCallOtherMicroservice(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getStatusCode')
            ->willReturn(200)
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                '/api/products/1',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Accept' => 'application/ld+json',
                        'Content-Type' => 'application/ld+json',
                    ],
                ],
            )
            ->willReturn($response)
        ;

        self::getContainer()->set('test.http_client', $httpClient);
        self::getContainer()->get('api_platform_ms.api_resource.existence_verifier')->verify('bar', '/api/products/1');
    }

    /**
     * @dataProvider provideSuccessStatusCodes
     */
    public function testParseSuccessfulMicroserviceResponse(int $statusCode): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse(info: ['http_code' => $statusCode]),
        ]);
        self::getContainer()->set('test.http_client', $httpClient);

        self::assertTrue(
            self::getContainer()->get('api_platform_ms.api_resource.existence_verifier')->verify('bar', '/api/products/1'),
        );
    }

    public static function provideSuccessStatusCodes(): iterable
    {
        yield [200];
        yield [203];
    }

    public function testParseNotFoundMicroserviceResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse(info: ['http_code' => 404]),
        ]);
        self::getContainer()->set('test.http_client', $httpClient);

        self::assertFalse(
            self::getContainer()->get('api_platform_ms.api_resource.existence_verifier')->verify('bar', '/api/products/1'),
        );
    }

    public function testErroredMicroserviceResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse(info: ['http_code' => 500]),
        ]);

        $this->expectException(\Throwable::class);

        self::getContainer()->set('test.http_client', $httpClient);
        self::getContainer()->get('api_platform_ms.api_resource.existence_verifier')->verify('bar', '/api/products/1');
    }

    public function testSwitchHttpClient(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        $firstHttpClient = $this->createMock(HttpClientInterface::class);
        $firstHttpClient->expects(self::once())->method('request')->willReturn($response);

        $secondHttpClient = $this->createMock(HttpClientInterface::class);
        $secondHttpClient->expects(self::once())->method('request')->willReturn($response);

        self::getContainer()->set('test.http_client', $firstHttpClient);

        $existenceVerifier = self::getContainer()->get('api_platform_ms.api_resource.existence_verifier');
        $existenceVerifier->verify('bar', '/api/products/1');

        $existenceVerifier->setWrappedHttpClient($secondHttpClient);
        $existenceVerifier->verify('bar', '/api/products/1');
    }
}
