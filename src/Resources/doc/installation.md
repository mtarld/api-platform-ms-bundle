# Getting started with Symbok
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
