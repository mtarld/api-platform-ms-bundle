# API resource existence constraint
## Motivation
Sometimes, resources of a microservice A depends on resource of a microservice B.
Then you may need to ensure that related resources are existing when validating a resource.

## Description
The `ApiResourceExists` constraint helps you to ensure that the related resource exists on the other microservice
when doing validation.

## Example
```php
use Mtarld\ApiPlatformMsBundle\Validator\ApiResourceExists;
use Symfony\Component\Validator\Constraints as Assert;

class Order
{
    /**
     * @var list<string>
     */
    #[Assert\All([
        new Assert\NotBlank(allowNull: false),
        new ApiResourceExists('product', regexPattern: '/^\/api\/products\/\d+$/'),
    ])]
    public array $products;

    #[ApiResourceExists(microservice: 'client', skipOnError: true)]
    public string $customer;
}
```
