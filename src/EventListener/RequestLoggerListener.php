<?php

namespace Mtarld\ApiPlatformMsBundle\EventListener;

use Mtarld\ApiPlatformMsBundle\Event\RequestEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

// Help opcache.preload discover always-needed symbols
class_exists(RequestEvent::class);
/**
 * @final
 *
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
class RequestLoggerListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onHttpRequest(RequestEvent $event): void
    {
        $this->logger->debug('Calling "{microservice_name}" microservice: "{method} {url}".', [
            'method' => $event->getMethod(),
            'microservice_name' => $event->getMicroservice()->getName(),
            'url' => $event->getUri(),
        ]);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onHttpRequest',
        ];
    }
}
