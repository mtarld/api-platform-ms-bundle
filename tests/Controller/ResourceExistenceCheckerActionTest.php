<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Controller;

use ApiPlatform\Core\Api\IriConverterInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group functional
 * @group resource-existence
 */
class ResourceExistenceCheckerActionTest extends WebTestCase
{
    public function testExistenceCheckWithUnsupportedContentType(): void
    {
        $client = static::createClient();
        $client->request('POST', '/foo_check_resource', [], [], [
            'CONTENT_TYPE' => 'unsupported',
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider existenceCheckBadRequestWithWrongBodyDataProvider
     * @testdox Bad request with content: $content
     */
    public function testExistenceCheckBadRequestWithWrongContent(?string $content): void
    {
        $client = static::createClient();
        $client->request('POST', '/foo_check_resource', [], [], ['CONTENT_TYPE' => 'application/json'], $content);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function existenceCheckBadRequestWithWrongBodyDataProvider(): iterable
    {
        yield [null];
        yield [''];
        yield ['{}'];
        yield ['{"iris": null}'];
        yield ['{"iris": "null}'];
        yield ['{"iris": "foo"}'];
        yield ['{"iris": [null]}'];
        yield ['{"iris": [""]}'];
        yield ['{"iris": [1]}'];
    }

    /**
     * @dataProvider successfulExistenceCheckDataProvider
     * @testdox Response body is $result when payload is $payload and resources existence is $exist
     */
    public function testSuccessfulExistenceCheck(bool $exist, string $payload, string $result): void
    {
        $iriConverter = $this->createMock(IriConverterInterface::class);
        if (!$exist) {
            $iriConverter
                ->method('getItemFromIri')
                ->willThrowException(new Exception())
            ;
        }

        $client = static::createClient();

        static::$container->set('test.iri_converter', $iriConverter);
        $client->request('POST', '/foo_check_resource', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], $payload);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($result, $client->getResponse()->getContent());
    }

    public function successfulExistenceCheckDataProvider(): iterable
    {
        yield [true, '{"iris": []}', '{"existences":[]}'];
        yield [true, '{"iris": ["foo"]}', '{"existences":{"foo":true}}'];
        yield [false, '{"iris": ["bar"]}', '{"existences":{"bar":false}}'];
    }
}
