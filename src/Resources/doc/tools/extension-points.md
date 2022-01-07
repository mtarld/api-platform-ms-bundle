# Extension points

## RequestEvent
**Fired just after any http client's request.**

Accessible properties:
- `getMicroservice(): Microservice`: the microservice targeted by the request.
- `getMethod(): string`: the request HTTP method.
- `getUri(): string`: the request URI.
- `getOptions(): array`: the options used for the request.

Example:
```php
<?php

namespace App\EventListener;

use Mtarld\ApiPlatformMsBundle\Event\RequestEvent;

class RequestDumper
{
    public function __invoke(RequestEvent $event): void
    {
        dump(sprintf(
            'Calling "%s" microservice: "%s %s".',
            $event->getMicroservice()->getName(),
            $event->getMethod(),
            $event->getUri(),
        ));
    }
}
```

```yaml
services:
    App\EventListener\RequestDumper:
        tags: ['kernel.event_listener']
```
