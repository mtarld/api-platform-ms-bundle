<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\HttpRepository;

use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto\PuppyResourceDto;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Entity\Puppy;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\HttpRepository\PuppyHttpRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @group repository
 * @group http
 */
class HttpRepositoryTest extends KernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    public function testFindExistingResourceByIri(): void
    {
        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($serializer->serialize(new Puppy(1, 'foo'), 'jsonld'))
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                '/api/puppies/1',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ]
            )
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $puppyDto = $httpRepository->findOneByIri('/puppies/1');
        self::assertEquals(new PuppyResourceDto('/puppies/1', 'foo'), $puppyDto);
    }

    public function testFindMissingResourceByIri(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('', ['http_code' => 404]),
        ]);

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $puppyDto = $httpRepository->findOneByIri('/puppies/1');
        self::assertNull($puppyDto);
    }

    public function testFindResourceBy(): void
    {
        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($serializer->serialize([new Puppy(1, 'foo'), new Puppy(2, 'bar')], 'jsonld'))
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                '/api/puppies?superName%5B0%5D=foo&superName%5B1%5D=bar',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ]
            )
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $puppyDtos = $httpRepository->findBy('superName', ['foo', 'bar']);
        self::assertCount(2, $puppyDtos);
        self::assertNotNull($puppyDtos->getMicroservice());
    }

    public function testFindOneExistingResourceBy(): void
    {
        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($serializer->serialize([new Puppy(1, 'foo')], 'jsonld'))
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                '/api/puppies?superName%5B0%5D=foo',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ]
            )
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $puppyDto = $httpRepository->findOneBy('superName', 'foo');
        self::assertEquals(new PuppyResourceDto('/puppies/1', 'foo'), $puppyDto);
    }

    public function testFindOneMissingResourceBy(): void
    {
        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($serializer->serialize([], 'jsonld'))
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                '/api/puppies?superName%5B0%5D=foo',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ]
            )
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $puppyDto = $httpRepository->findOneBy('superName', 'foo');
        self::assertNull($puppyDto);
    }

    public function testFindAllResources(): void
    {
        /** @var SerializerInterface $serializer */
        $serializer = static::$container->get(SerializerInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getContent')
            ->willReturn($serializer->serialize([new Puppy(1, 'foo'), new Puppy(2, 'bar')], 'jsonld'))
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                '/api/puppies',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ]
            )
            ->willReturn($response)
        ;

        static::$container->set('test.http_client', $httpClient);

        /** @var PuppyHttpRepository $httpRepository */
        $httpRepository = static::$container->get(PuppyHttpRepository::class);

        $puppyDtos = $httpRepository->findAll();
        self::assertCount(2, $puppyDtos);
        self::assertNotNull($puppyDtos->getMicroservice());
    }
}
