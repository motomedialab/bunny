<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Integrations\Requests\ManageCollections;

use Illuminate\Http\Client\Response;
use Motomedialab\Bunny\Data\ApiError;
use Motomedialab\Connector\BaseRequest;
use Motomedialab\Connector\Enums\RequestMethod;
use Motomedialab\Connector\Contracts\RequestInterface;
use Motomedialab\Bunny\Data\ManageCollections\CollectionData;

/**
 * @implements RequestInterface<CollectionData|ApiError>
 */
readonly class CreateCollectionRequest extends BaseRequest implements RequestInterface
{
    public function __construct(public int $libraryId, public string $name)
    {
        //
    }

    public function endpoint(): string
    {
        return "library/$this->libraryId/collections";
    }

    public function method(): RequestMethod
    {
        return RequestMethod::POST;
    }

    public function body(): array
    {
        return [
            'name' => $this->name,
        ];
    }

    public function toResponse(Response $response): CollectionData|ApiError
    {
        return $response->successful()
            ? CollectionData::fromResponse($response)
            : ApiError::fromResponse($response);
    }
}
