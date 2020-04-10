# API resource existence constraint
## Motivation
Sometimes, resources of a microservice A depends on resource of a microservice B.
Then you may need to ensure that related resources are existing when validating a resource.

## Description
The `ApiResourceExist` constraint helps you to ensure that the related resources are existing on the other microservice
when doing validation.
It can verify either IRIs and list of IRIs.

## Example
```php
use Mtarld\ApiPlatformMsBundle\Validator\ApiResourceExist;

class Order
{
    /**
     * @var string[]
     *
     * @ApiResourceExist("product")
     */
    public $products;

    /**
     * @var string
     *
     * @ApiResourceExist(microservice=client, skipOnError=true)
     */
    public $customer;
}
```
