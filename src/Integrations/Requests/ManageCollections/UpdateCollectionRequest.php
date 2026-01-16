<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Integrations\Requests\ManageCollections;

use Illuminate\Http\Client\Response;
use Motomedialab\Bunny\Data\ApiError;
use Motomedialab\Connector\BaseRequest;
use Motomedialab\Connector\Enums\RequestMethod;
use Motomedialab\Connector\Contracts\RequestInterface;

/**
 * @implements RequestInterface<true|ApiError>
 */
readonly class UpdateCollectionRequest extends BaseRequest implements RequestInterface
{
    public function __construct(public int $libraryId, public string $collectionId)
    {
        //
    }

    public function endpoint(): string
    {
        return "library/$this->libraryId/collections/$this->collectionId";
    }

    public function method(): RequestMethod
    {
        return RequestMethod::POST;
    }

    public function toResponse(Response $response): true|ApiError
    {
        return $response->json('success') ? true : ApiError::fromResponse($response);
    }
}
