# Resource collection and pagination
## Motivation
Calling for a API resource collection usually mean dealing with pagination.
This mean that one API call isn't supposed to return every element of the collection.
Then, it may be handy to have objects and services in order the ease paginated collection iteration.

## Description
- The `Collection` object holds either the partial collection view and the pagination metadata. 
- The `PaginatedCollectionIterator` can be used to iterate over the whole collection by doing internally the HTTP calls.

## Example
### Collection object
```php
use App\Dto\ProductDto;
use Mtarld\ApiPlatformMsBundle\Collection\Collection;
use Mtarld\ApiPlatformMsBundle\HttpClient\GenericHttpClient;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Symfony\Component\Serializer\SerializerInterface;

class ProductService
{
    private $serializer;
    private $httpClient;
    private $microservices;

    public function __construct(
        SerializerInterface $serializer,
        GenericHttpClient $httpClient,
        MicroservicePool $microservices
    ) {
        $this->serializer = $serializer;
        $this->httpClient = $httpClient;
        $this->microservices = $microservices;
    }

    /**
     * Count the whole collection
     */
    public function countProducts(): int
    {
        $productMicroservice = $this->microservices->get('product');
        $response = $this->httpClient->request($productMicroservice, 'GET', '/products');

        /** @var Collection $products */
        $products = $this->serializer->deserialize($response->getContent(), Collection::class.'<'.ProductDto::class.'>', $productMicroservice->getFormat());
        
        return $products->count();
    }

    /**
     * Iterate over one page
     */
    public function getProductsName(int $page): iterable
    {
        $productMicroservice = $this->microservices->get('product');
        $response = $this->httpClient->request($productMicroservice, 'GET', '/products?page='.$page);

        /** @var Collection $products */
        $products = $this->serializer->deserialize($response->getContent(), Collection::class.'<'.ProductDto::class.'>', $productMicroservice->getFormat());
        
        foreach ($products as $product) {
            yield $product->getName();
        }
    }

    /**
     * Get pagination information
     */
    public function isLastPage(int $page): bool
    {
        $productMicroservice = $this->microservices->get('product');
        $response = $this->httpClient->request($productMicroservice, 'GET', '/products?page='.$page);

        /** @var Collection $products */
        $products = $this->serializer->deserialize($response->getContent(), Collection::class.'<'.ProductDto::class.'>', $productMicroservice->getFormat());
        
        return $products->getPagination()->getCurrent() === $products->getPagination()->getLast();
    }
}
```

### PaginatedCollectionIterator service
```php
use App\Dto\ProductDto;
use Mtarld\ApiPlatformMsBundle\Collection\Collection;
use Mtarld\ApiPlatformMsBundle\Collection\PaginatedCollectionIterator;
use Mtarld\ApiPlatformMsBundle\HttpClient\GenericHttpClient;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Symfony\Component\Serializer\SerializerInterface;

class ProductService
{
    private $serializer;
    private $httpClient;
    private $microservices;
    private $collectionIterator;

    public function __construct(
        SerializerInterface $serializer,
        GenericHttpClient $httpClient,
        MicroservicePool $microservices,
        PaginatedCollectionIterator $collectionIterator
    ) {
        $this->serializer = $serializer;
        $this->httpClient = $httpClient;
        $this->microservices = $microservices;
        $this->collectionIterator = $collectionIterator;
    }

    /**
     * Iterate over the whole collection
     */
    public function getProductsDtoCollection(): iterable
    {
        $productMicroservice = $this->microservices->get('product');
        $response = $this->httpClient->request($productMicroservice, 'GET', '/products');

        /** @var Collection $products */
        $products = $this->serializer->deserialize($response->getContent(), Collection::class.'<'.ProductDto::class.'>', $productMicroservice->getFormat());
        
        yield from $this->collectionIterator->iterateOver($products);
    }
}
```
