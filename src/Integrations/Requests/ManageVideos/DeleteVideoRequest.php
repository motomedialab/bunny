<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Integrations\Requests\ManageVideos;

use Illuminate\Http\Client\Response;
use Motomedialab\Bunny\Data\ApiError;
use Motomedialab\Connector\BaseRequest;
use Motomedialab\Connector\Enums\RequestMethod;
use Motomedialab\Connector\Contracts\RequestInterface;

/**
 * @implements RequestInterface<true|ApiError>
 * @see https://docs.bunny.net/reference/video_deletevideo
 */
readonly class DeleteVideoRequest extends BaseRequest implements RequestInterface
{
    use IsVideoRequest;

    public function method(): RequestMethod
    {
        return RequestMethod::DELETE;
    }

    public function toResponse(Response $response): true|ApiError
    {
        return $response->successful() ? true : ApiError::fromResponse($response);
    }
}
