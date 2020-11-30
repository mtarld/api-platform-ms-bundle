# HTTP repository
## Motivation
To improve the code separation, you may want to get rid of the HTTP related stuff from your services.
Therefore, it may be useful to have an abstract repository that encapsulate HTTP calls.

## Description
The `AbstractMicroserviceHttpRepository` is an extendable repository that you could configure to fetch and alter resources.
It will take care of HTTP calls and serialization/deserialization so you'll only have to deal with DTOs:

## Example
```php
use Mtarld\ApiPlatformMsBundle\HttpRepository\AbstractMicroserviceHttpRepository;

class ProductHttpRepository extends AbstractMicroserviceHttpRepository
{
    /**
     * Custom method
     */
    public function fetchOneByName(string $name): ?ProductDto
    {
        return parent::fetchOneBy([
            'name' => $name,
        ]);
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
            'product' => $productHttpRepository->fetchOneByName($request->query->get('name')),
        ]);
    }

    public function updateFirstProduct(ProductHttpRepository $productHttpRepository)
    {
        $product = $productHttpRepository->fetchOneByIri('/products/1');
        $product->setName('new name');

        $productHttpRepository->update($product);
    }
}
```
