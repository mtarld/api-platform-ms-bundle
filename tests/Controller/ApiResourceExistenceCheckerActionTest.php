<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\Controller;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Symfony\Validator\Exception\ValidationException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @group resource-existence
 *
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 */
class ApiResourceExistenceCheckerActionTest extends WebTestCase
{
    public function testExistenceCheckWithUnsupportedContentType(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $client = static::createClient();
        $client->catchExceptions(false);

        $client->request('POST', '/foo_check_resource', [], [], ['CONTENT_TYPE' => 'unsupported']);
    }

    /**
     * @dataProvider existenceCheckBadRequestWithWrongBodyDataProvider
     *
     * @testdox Bad request with content: $content
     */
    public function testExistenceCheckBadRequestWithWrongContent(?string $content, string $exceptionFqcn): void
    {
        $this->expectException($exceptionFqcn);

        $client = static::createClient();
        $client->catchExceptions(false);

        $client->request('POST', '/foo_check_resource', [], [], ['CONTENT_TYPE' => 'application/json'], $content);
    }

    public function existenceCheckBadRequestWithWrongBodyDataProvider(): iterable
    {
        yield [null, BadRequestHttpException::class];
        yield ['', BadRequestHttpException::class];
        yield ['{}', BadRequestHttpException::class];
        yield ['{"iris": null}', BadRequestHttpException::class];
        yield ['{"iris": "null}', BadRequestHttpException::class];
        yield ['{"iris": "foo"}', BadRequestHttpException::class];
        yield ['{"iris": [null]}', ValidationException::class];
        yield ['{"iris": [""]}', ValidationException::class];
        yield ['{"iris": [1]}', ValidationException::class];
    }

    /**
     * @dataProvider successfulExistenceCheckDataProvider
     *
     * @testdox Response body is $result when payload is $payload and resources existence is $exist
     */
    public function testSuccessfulExistenceCheck(bool $exist, string $payload, string $result): void
    {
        $iriConverter = $this->createMock(IriConverterInterface::class);
        if (!$exist) {
            $iriConverter
                ->method('getResourceFromIri')
                ->willThrowException(new \Exception())
            ;
        }

        $client = static::createClient();

        static::getContainer()->set('test.iri_converter', $iriConverter);
        $client->request('POST', '/foo_check_resource', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], $payload);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals($result, $client->getResponse()->getContent());
    }

    public function successfulExistenceCheckDataProvider(): iterable
    {
        yield [true, '{"iris": []}', '{"existences":[]}'];
        yield [true, '{"iris": ["foo"]}', '{"existences":{"foo":true}}'];
        yield [false, '{"iris": ["bar"]}', '{"existences":{"bar":false}}'];
    }
}
