<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Integrations\Requests\ManageVideos;

use Illuminate\Http\Client\Response;
use Motomedialab\Bunny\Data\ApiError;
use Motomedialab\Connector\BaseRequest;
use Motomedialab\Connector\Enums\RequestMethod;
use Motomedialab\Bunny\Data\Payloads\UpdateVideo;
use Motomedialab\Connector\Contracts\RequestInterface;
use Motomedialab\Bunny\Integrations\Requests\Traits\IsVideoRequest;

/**
 * @implements RequestInterface<true|ApiError>
 * @see https://docs.bunny.net/reference/video_updatevideo
 */
readonly class UpdateVideoRequest extends BaseRequest implements RequestInterface
{
    use IsVideoRequest {
        IsVideoRequest::__construct as traitConstructor;
    }

    public function __construct(
        int $libraryId,
        string $videoId,
        public UpdateVideo $updateVideo,
    ) {
        $this->traitConstructor($libraryId, $videoId);
    }

    public function method(): RequestMethod
    {
        return RequestMethod::POST;
    }

    public function body(): array
    {
        return $this->updateVideo->toArray();
    }

    public function toResponse(Response $response): true|ApiError
    {
        return when($response->ok(), true, fn (): ApiError => ApiError::fromResponse($response));
    }
}
