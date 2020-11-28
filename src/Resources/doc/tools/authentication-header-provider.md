# Authentication header providers
## Motivation
Most of the time, microservices will need an authentication header.
In that way, they will know who is doing the request and to authorize
it to interact with a resource or not.

Even if authentication headers are part of the request headers,
we don't want to specify them each time we call the `request` method.
Therefore, we can use authentication header providers to inject authentication headers
in requests and decouple them from the regular headers.

## Description
Create your Provider and implements `AuthenticationHeaderProviderInterface`.
Behind the scene the Http Client will iterate over them and call `AuthenticationHeaderProviderInterface::supports` in order to fetch the good provider.

You can refer to the [Symfony documentation](https://symfony.com/doc/current/service_container/tags.html) to know more about Service Tags (autoconfiguration, priority...)

## Here is an example
```yaml
services:
    _defaults:
        autoconfigure: true
```

```php
use App\User\Security\OAuthAccessTokenInterface;
use Mtarld\ApiPlatformMsBundle\HttpClient\AuthenticationHeaderProviderInterface;
class BearerAuthenticationHeaderProvider implements AuthenticationHeaderProviderInterface
{
    private $accessToken;
    public function __construct(OAuthAccessTokenInterface $accessToken)
    {
        $this->accessToken = $accessToken;
    }
    public function getHeader() : string
    {
        return 'Authorization';
    }
    public function getValue() : string
    {
        return sprintf('Bearer %s', $this->accessToken->getOAuthAccessToken());
    }
    public function supports(array $context) : bool
    {
        // $context is an hashmap containing 'method', 'uri', 'options' and 'microservice'.
        return null !== $this->accessToken->getOAuthAccessToken() && 'product' === $context['microservice']->getName();
    }
    public static function getDefaultPriority(): int
    {
        return 10;
    }
}
```