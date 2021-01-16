<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\HttpClient;

use Mtarld\ApiPlatformMsBundle\HttpClient\GenericHttpClient;
use Mtarld\ApiPlatformMsBundle\HttpClient\MicroserviceHttpClientInterface;
use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Authentication\BasicAuthenticationHeaderProvider;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Authentication\BearerAuthenticationHeaderProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @group http
 * @group client
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class HttpClientTest extends KernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    public function testGenericClient(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::exactly(2))
            ->method('request')
            ->withConsecutive([
                'GET',
                '/api/puppies',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ],
            ], [
                'POST',
                '/api/puppies',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'body' => '{"foo":"bar"}',
                ],
            ])
            ->willReturn($this->createMock(ResponseInterface::class))
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var GenericHttpClient $genericHttpClient */
        $genericHttpClient = static::$container->get(GenericHttpClient::class);

        /** @var Microservice $microservice */
        $microservice = static::$container->get(MicroservicePool::class)->get('bar');

        $genericHttpClient->request($microservice, 'GET', '/puppies');
        $genericHttpClient->request($microservice, 'POST', '/puppies', ['foo' => 'bar'], 'application/json', 'json');
    }

    public function testSpecificMicroserviceHttpClient(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::exactly(2))
            ->method('request')
            ->withConsecutive([
                'GET',
                '/api/puppies',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ],
            ], [
                'POST',
                '/api/puppies',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'body' => '{"foo":"bar"}',
                ],
            ])
            ->willReturn($this->createMock(ResponseInterface::class))
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var MicroserviceHttpClientInterface $microserviceHttpClient */
        $microserviceHttpClient = static::$container->get('api_platform_ms.http_client.microservice.bar');

        $microserviceHttpClient->request('GET', '/puppies');
        $microserviceHttpClient->request('POST', '/puppies', ['foo' => 'bar'], 'application/json', 'json');
    }

    public function testSwitchHttpClient(): void
    {
        $firstHttpClient = $this->createMock(HttpClientInterface::class);
        $firstHttpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->createMock(ResponseInterface::class))
        ;

        $secondHttpClient = $this->createMock(HttpClientInterface::class);
        $secondHttpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->createMock(ResponseInterface::class))
        ;

        static::$container->set('test.http_client', $firstHttpClient);

        /** @var MicroserviceHttpClientInterface $microserviceHttpClient */
        $microserviceHttpClient = static::$container->get('api_platform_ms.http_client.microservice.bar');

        $microserviceHttpClient->request('GET', '/puppies');

        $microserviceHttpClient->setWrappedHttpClient($secondHttpClient);
        $microserviceHttpClient->request('GET', '/puppies');
    }

    /**
     * @dataProvider authenticationHeadersDataProvider
     */
    public function testAuthenticationHeaders(string $microservice, string $method, $uri, array $expectedAuthenticationHeader): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                $method,
                $uri,
                [
                    'base_uri' => 'https://localhost',
                    'headers' => ['Content-Type' => 'application/ld+json', 'Accept' => 'application/ld+json'] + $expectedAuthenticationHeader,
                ]
            )
            ->willReturn($this->createMock(ResponseInterface::class))
        ;
        static::$container->set('test.http_client', $httpClient);

        static::$container->get(BasicAuthenticationHeaderProvider::class)->setEnabled(true);
        static::$container->get(BearerAuthenticationHeaderProvider::class)->setEnabled(true);

        static::$container->get(GenericHttpClient::class)->request(static::$container->get(MicroservicePool::class)->get($microservice), $method, $uri);
    }

    public function authenticationHeadersDataProvider(): iterable
    {
        yield ['bar', 'POST', '/api/puppies', []];
        yield ['bar', 'GET', '/api/puppies', ['Authorization' => 'Basic password']];
        yield ['bar', 'DELETE', '/api/dummies', ['Authorization' => 'Bearer bearer']];
    }
}
