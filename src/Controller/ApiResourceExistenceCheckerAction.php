<?php

namespace Mtarld\ApiPlatformMsBundle\Controller;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceExistenceCheckerPayload;
use Mtarld\ApiPlatformMsBundle\Dto\ApiResourceExistenceCheckerView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

// Help opcache.preload discover always-needed symbols
class_exists(ApiResourceExistenceCheckerPayload::class);
class_exists(ApiResourceExistenceCheckerView::class);
class_exists(BadRequestHttpException::class);
class_exists(JsonResponse::class);

/**
 * @final @internal
 */
class ApiResourceExistenceCheckerAction
{
    private $serializer;
    private $iriConverter;
    private $validator;

    public function __construct(
        SerializerInterface $serializer,
        IriConverterInterface $iriConverter,
        ValidatorInterface $validator
    ) {
        $this->serializer = $serializer;
        $this->iriConverter = $iriConverter;
        $this->validator = $validator;
    }

    public function __invoke(Request $request): JsonResponse
    {
        if (null === $contentType = $request->getContentType()) {
            throw new BadRequestHttpException('Content type is not supported');
        }

        /** @var string $content */
        if (empty($content = $request->getContent())) {
            throw new BadRequestHttpException('Content is missing');
        }

        try {
            /** @var ApiResourceExistenceCheckerPayload $payload */
            $payload = $this->serializer->deserialize($content, ApiResourceExistenceCheckerPayload::class, $contentType);
        } catch (Throwable $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $this->validator->validate($payload);

        $existences = [];
        foreach ($payload->iris as $iri) {
            $existences[$iri] = $this->isIriValid($iri);
        }

        return new JsonResponse(new ApiResourceExistenceCheckerView($existences));
    }

    private function isIriValid(string $iri): bool
    {
        try {
            $this->iriConverter->getItemFromIri($iri);
        } catch (Throwable $exception) {
            return false;
        }

        return true;
    }
}
