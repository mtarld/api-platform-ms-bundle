# HTTP repository
## Motivation
To improve the code separation, you may want to get rid of the HTTP related stuff from your services.
Therefore, it may be useful to have an abstract repository that encapsulate HTTP calls.

## Description
The `AbstractMicroserviceHttpRepository` is an extendable repository that you could configure to fetch resources.
It will take care of HTTP calls and deserialization so you'll only have to deal with DTOs:

## Example
```php
use Mtarld\ApiPlatformMsBundle\HttpRepository\AbstractMicroserviceHttpRepository;

class ProductHttpRepository extends AbstractMicroserviceHttpRepository
{
    /**
     * Custom method
     */
    public function findOneByName(string $name): ?ProductDto
    {
        return parent::findOneBy('name', $name);
    }

    protected function getMicroserviceName(): string
    {
        return 'product';
    }

    protected function getResourceEndpoint(): string
    {
        return 'products';
    }

    protected function getResourceDto(): string
    {
        return ProductDto::class;
    }
}
```

```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    public function byName(Request $request, ProductHttpRepository $productHttpRepository)
    {
        return $this->render('product.html.twig', [
            'product' => $productHttpRepository->findOneByName($request->query->get('name')),
        ]);
    }

    public function firstEverProduct(ProductHttpRepository $productHttpRepository)
    {
        return $this->render('product.html.twig', [
            'product' => $productHttpRepository->findOneByIri('/products/1'),
        ]);
    }
}
```
