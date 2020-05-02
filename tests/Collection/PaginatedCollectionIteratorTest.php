<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Collection;

use Mtarld\ApiPlatformMsBundle\Collection\Collection;
use Mtarld\ApiPlatformMsBundle\Collection\PaginatedCollectionIterator;
use Mtarld\ApiPlatformMsBundle\Collection\Pagination;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Mtarld\ApiPlatformMsBundle\Tests\Fixtures\App\src\Dto\PuppyResourceDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @group collection
 * @group http
 */
class PaginatedCollectionIteratorTest extends KernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    public function testIterateOverPaginatedCollection(): void
    {
        $this->mockHttpClient();

        $collection = new Collection([
            new PuppyResourceDto('/puppies/1', 1, 'foo'),
            new PuppyResourceDto('/puppies/2', 1, 'bar'),
        ], 5, new Pagination('/puppies?page=1', '/puppies?page=1', '/puppies?page=3', null, '/puppies?page=2'));

        $expectedElements = [
            new PuppyResourceDto('/puppies/1', 1, 'foo'),
            new PuppyResourceDto('/puppies/2', 1, 'bar'),
            new PuppyResourceDto('/puppies/3', 3, 'baz'),
            new PuppyResourceDto('/puppies/4', 4, 'oof'),
            new PuppyResourceDto('/puppies/5', 5, 'rab'),
        ];

        /** @var MicroservicePool $microservices */
        $microservices = static::$container->get('api_platform_ms.microservice_pool');

        /** @var PaginatedCollectionIterator $iterator */
        $iterator = static::$container->get(PaginatedCollectionIterator::class);

        $index = 0;
        foreach ($iterator->iterateOver($collection, $microservices->get('bar')) as $element) {
            $this->assertEquals($expectedElements[$index++], $element);
        }

        $this->assertEquals(5, $index);
    }

    private function mockHttpClient(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->exactly(2))
            ->method('request')
            ->withConsecutive([
                'GET',
                '/puppies?page=2',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ],
            ], [
                'GET',
                '/puppies?page=3',
                [
                    'base_uri' => 'https://localhost',
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                        'Accept' => 'application/ld+json',
                    ],
                ],
            ])
            ->willReturnOnConsecutiveCalls(...$this->getPartialCollectionResponses())
        ;

        static::$container->set('test.http_client', $httpClient);
    }

    private function getPartialCollectionResponses(): array
    {
        $page2Response = $this->createMock(ResponseInterface::class);
        $page2Response
            ->method('getContent')
            ->willReturn(<<<JSON
{
    "@context":"\/contexts\/Puppy", "@id":"\/puppies", "@type":"hydra:Collection", "hydra:totalItems":5,
    "hydra:member":[{"@id":"\/puppies\/3","@type":"Puppy","id":3,"super_name":"baz"}, {"@id":"\/puppies\/4","@type":"Puppy","id":4,"super_name":"oof"}],
    "hydra:view": {
        "@id": "\/puppies?page=2",
        "hydra:first": "\/puppies?page=1",
        "hydra:last": "\/puppies?page=3",
        "hydra:previous": "\/puppies?page=1",
        "hydra:next": "\/puppies?page=3"
    }
}
JSON
            )
        ;

        $page3Response = $this->createMock(ResponseInterface::class);
        $page3Response
            ->method('getContent')
            ->willReturn(<<<JSON
{
    "@context":"\/contexts\/Puppy", "@id":"\/puppies", "@type":"hydra:Collection", "hydra:totalItems":5,
    "hydra:member":[{"@id":"\/puppies\/5","@type":"Puppy","id":5,"super_name":"rab"}],
    "hydra:view": {
        "@id": "\/puppies?page=3",
        "hydra:first": "\/puppies?page=1",
        "hydra:last": "\/puppies?page=3",
        "hydra:previous": "\/puppies?page=2"
    }
}
JSON
            )
        ;

        return [$page2Response, $page3Response];
    }
}
