# ConstraintViolation list denormalizer
## Motivation
If a microservice A tries to update a resource from a microservice B, and somehow the updated resource
isn't valid due to constraint violations,
then it may useful to retrieve in the microservice A the constraint violations. 

For example, you may want to be able to propagate a `ValidationException`
to the client whoever has initially thrown the exception.

## Description
The `ConstraintViolationListDenormalizer` allows you to create a `ConstraintViolationList`
instance from a serialized string.

## Example
```php
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Dto\ProductDto;
use Mtarld\ApiPlatformMsBundle\HttpClient\GenericHttpClient;
use Mtarld\ApiPlatformMsBundle\Microservice\MicroservicePool;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class ProductController
{
    public function createProduct(SerializerInterface $serializer, GenericHttpClient $httpClient, MicroservicePool $microservices): Response
    {
        $productMicroservice = $microservices->get('product');
        $response = $httpClient->request($productMicroservice, 'POST', '/products', new ProductDto());

        // Something went wrong with submitted data
        if (400 === $response->getStatusCode()) {
            /** @var ConstraintViolationList $violations */
            $violations =  $serializer->deserialize($response->getContent(), ConstraintViolationList::class, $productMicroservice->getFormat());
            if (null !== $violations) {
                throw new ValidationException($violations);
            }
        }

        return new Response('', 201);
    }
}
```
