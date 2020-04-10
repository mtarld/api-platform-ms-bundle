# API Platform Microservice Bundle

![Packagist](https://img.shields.io/packagist/v/mtarld/api-platform-ms-bundle.svg?style=flat-square)
![GitHub](https://img.shields.io/github/license/mtarld/api-platform-ms-bundle.svg?style=flat-square)
![Travis (.org)](https://img.shields.io/travis/mtarld/api-platform-ms-bundle.svg?style=flat-square)

Microservice tools bundles for API Platform.

## A microservice bundle ?

## Getting started
### Installation
You can easily install API Platform Microservice bundle by composer
```
$ composer require mtarld/api-platform-ms-bundle
```
Then, bundle should be registered. Just verify that `config\bundles.php` is containing :
```php
Mtarld\ApiPlaformMsBundle\ApiPlaformMsBundle::class => ['all' => true]
```

### Configuration
Once Symbok is installed, you should configure it to fit your needs. 

To do so, edit `config/packages/api-platform-ms-bundle.yaml`
```yaml
# config/packages/api-platform-ms-bundle.yaml

api-plaform-ms:
    # TODO
```
And you're ready to go ! :rocket:

## Provided elements

## Documentation
A detailed documentation is available [here](src/Resources/doc/index.md)

## Contributing
Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

After writing your fix/feature, you can run following commands to make sure that everyting is still ok.

```bash
# Install dev dependencies
$ composer install

# Running tests locally
$ make test

```

## Authors
 - Mathias Arlaud - [mtarld](https://github.com/mtarld) - <mathias(dot)arlaud@gmail(dot)com>
