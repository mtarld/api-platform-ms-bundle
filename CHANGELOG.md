# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.0] - Unreleased
* Allowed to replace nested HttpClient during runtime thanks to `setHttpClient`
* Removed useless `api_platform_ms.http_repository.http_repository` service definition
* Added query params to AbstractHttpMicroserviceRepository
* Added nested resource denormalization
* Fixed lowest symfony/property-access version to 4.4
* Replaced Travis CI by Github actions
* Removed PHPCPD QA checks

## [0.1.1] - 2020-05-12
* Denormalized objects using APIP denormalizers (except for HAL because APIP doesn't handle it)

## [0.1.0] - 2020-05-10
* Added microservice specific HTTP client
* Added generic HTTP client
* Added Abstract HTTP repository
* Added collection, pagination and paginated collection iterator
* Added ConstraintViolationList denormalizers
* Added Collection denormalizers
* Added ApiResource denormalizers
* Added Object denormalizers
* Added `ApiResourceExist` constraint
