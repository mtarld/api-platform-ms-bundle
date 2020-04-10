# API Platform Microservice Bundle

![Packagist](https://img.shields.io/packagist/v/mtarld/api-platform-ms-bundle.svg?style=flat-square)
![GitHub](https://img.shields.io/github/license/mtarld/api-platform-ms-bundle.svg?style=flat-square)
![Travis (.org)](https://img.shields.io/travis/mtarld/api-platform-ms-bundle.svg?style=flat-square)

A Microservice tools bundle for API Platform.

## A microservice bundle ?
In a microservice context where each microservices are API Platform instances,
each instance mush behave as an API Platform data producer and an
API Platform data consumer.

But API Platform isn't intended to behave like a client.

Therefore, here comes the API Platform Microservice Bundle!

This bundle intents to provide a set of [tools](#provided-tools)
to ease the development of client behaving microservices by trying to abstract the http call layer.

## Getting started
### Installation
You can easily install API Platform Microservice bundle by composer
```
$ composer require mtarld/api-platform-ms-bundle
```
Then, bundle should be registered. Just verify that `config\bundles.php` is containing :
```php
Mtarld\ApiPlaformMsBundle\ApiPlaformMsBundle::class => ['all' => true],
```

### Configuration
Once the bundle is installed, you should configure it to fit your needs. 

To do so, edit `config/packages/api-platform-ms-bundle.yaml`
```yaml
# config/packages/api-platform-ms-bundle.yaml

api_plaform_ms:
    # HttpClient that will be used internally (default: 'Symfony\Contracts\HttpClient\HttpClientInterface')
    http_client: ~

    # Name of the current microservice (required)
    name: client

    # Host used for microservice dynamic routes generation (default: [])
    hosts:
        - https://client.api

    # List of related microservices
    microservices:
        # Microservice name
        product:
            # Microservice base uri (required)
            base_uri: https://product.api

            # Microservice format (required)
            # Supported formats: jsonld, jsonhal, jsonapi
            format: jsonld
```
And you're ready to go ! :rocket:

## Provided tools
- [**API resource existence constraint**](src/Resources/doc/tools/existence-constraint.md):
  Help you to ensure that the related resources are existing on the other microservice when doing validation.
  
- [**Resource collection and pagination**](src/Resources/doc/tools/pagination.md):
  Handy objects and services to ease paginated collection iteration.
  
- [**HTTP repository**](src/Resources/doc/tools/http-repository.md):
  An extendable HTTP repository that you could configure to fetch resources.

- [**HTTP client wrappers**](src/Resources/doc/tools/http-wrapper.md):
  Clients that adapt HTTP calls according to the targeted microservice configuration.

- [**ConstraintViolation list denormalizer**](src/Resources/doc/tools/constraint-violation-list.md)
  Allows you to create a `ConstraintViolationList` instance from a serialized string.

## Supported microservice formats
Currently, API Platform supported formats are:
- jsonld
- jsonapi
- jsonhal

## Contributing
Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

After writing your fix/feature, you can run following commands to make sure that everything is still ok.

```bash
# Install dev dependencies
$ composer install

# Running tests locally
$ make test

```

## Authors
 - Mathias Arlaud - [mtarld](https://github.com/mtarld) - <mathias(dot)arlaud@gmail(dot)com>
