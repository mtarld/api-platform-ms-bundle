<?php

namespace Mtarld\ApiPlatformMsBundle\Tests\EventListener;

use Mtarld\ApiPlatformMsBundle\Event\RequestEvent;
use Mtarld\ApiPlatformMsBundle\EventListener\RequestLoggerListener;
use Mtarld\ApiPlatformMsBundle\Microservice\Microservice;
use Mtarld\ApiPlatformMsBundle\Tests\BcLayerKernelTestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class RequestLoggerListenerTest extends BcLayerKernelTestCase
{
    public function setUp(): void
    {
        static::bootKernel();
    }

    public function microserviceDataProvider(): iterable
    {
        yield [new Microservice($msName = 'product', 'https://localhost', '/api', 'jsonld'),  $msName, 'GET', '/products'];
        yield [new Microservice($msName = 'user', 'https://domain', '/app', 'json'), $msName, 'DELETE', '/users'];
    }

    /**
     * @dataProvider microserviceDataProvider
     */
    public function testRequestIsLoggedViaAnEvent(Microservice $microservice, string $microserviceName, string $method, string $uri): void
    {
        $event = new RequestEvent($microservice, $method, $uri, []);
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('debug')
            ->with('Calling "{microservice_name}" microservice: "{method} {url}".', [
                'method' => $method,
                'microservice_name' => $microserviceName,
                'url' => $uri,
            ])
        ;

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(RequestEvent::class, new RequestLoggerListener($logger));
        $dispatcher->dispatch($event);
    }

    public function testRequestListenerIsNotRegisteredByDefault(): void
    {
        /** @var EventDispatcher $dispatcher */
        $dispatcher = static::$kernel->getContainer()->get('event_dispatcher');
        self::assertFalse($dispatcher->hasListeners(RequestEvent::class));
    }
}
