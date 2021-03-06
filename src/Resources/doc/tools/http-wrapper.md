# Microservice HTTP client wrappers
## Motivation
Each microservice specifics should be abstracted from the current microservice code.
A minimal number of classes should be aware of base URI or format of other microservices.
Therefore, these specifics can be abstracted by a wrapper.

## Description
`MicroserviceHttpClientInterface` is an HTTP client wrapper that adapt HTTP calls
according to the targeted microservice configuration.
This service could be retrieve by the id: `api_platform_ms.http.client.{microservice_name}`

If the targeted microservice could only be known at runtime, the `HttpClient` class allows
you to request any microservice given as method argument.

Furthermore, if you need to replace the real HTTP client during runtime
(which could be useful when using [scoped clients](https://symfony.com/doc/current/http_client.html#scoping-client)),
you can use the `setWrappedHttpClient` method of any `ReplaceableHttpClientInterface`.

## Example
```yaml
services:
    ProductService:
        arguments:
            $productHttpClient: 'api_platform_ms.http_client.microservice.product'
```

```php
use Mtarld\ApiPlatformMsBundle\HttpClient\GenericHttpClient;
use Mtarld\ApiPlatformMsBundle\HttpClient\MicroserviceHttpClientInterface;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;

class ProductService
{
    private $httpClient;
    private $productHttpClient;
    private $microservices;

    public function __construct(
        GenericHttpClient $httpClient,
        MicroserviceHttpClientInterface $productHttpClient,
        MicroservicePool $microservices
    ) {
        $this->httpClient = $httpClient;
        $this->productHttpClient = $productHttpClient;
        $this->microservices = $microservices;
    }

    public function updateProductName(int $id, string $name): void
    {
        $productMicroservice = $this->microservices->get('product');
        $this->httpClient->request($productMicroservice, 'PUT', '/products/'.$id, ['name' => 'newName']);
    }

    public function createEmptyProduct(): void
    {
        $this->productHttpClient->request('POST', '/products', new ProductDto());
    }
}
```
#### Replacing the real HTTP client during runtime
```php
use Mtarld\ApiPlatformMsBundle\HttpClient\GenericHttpClient;
use Mtarld\ApiPlatformMsBundle\HttpClient\MicroserviceHttpClientInterface;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;

class ProductService
{
    private $productHttpClient;
    private $superPrivilegedClient;

    public function __construct(
        MicroserviceHttpClientInterface $productHttpClient,
        HttpClientInterface $superPrivilegedClient
    ) {
        $this->productHttpClient = $productHttpClient;
        $this->superPrivilegedClient = $superPrivilegedClient;
    }

    public function createProduct(): void
    {
        $response = $this->productHttpClient->request('PUT', '/products/uuid', ['enabled' => true]);

        // retry with higher privileges
        if (403 === $response->getStatusCode()) {
            $this->productHttpClient->setWrappedHttpClient($this->superPrivilegedClient);
            $this->productHttpClient->request('PUT', '/products/uuid', ['enabled' => true]);
        }
    }
}
