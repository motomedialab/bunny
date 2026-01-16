<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Integrations\Requests\ManageVideos;

use Illuminate\Http\Client\Response;
use Motomedialab\Bunny\Data\ApiError;
use Motomedialab\Connector\BaseRequest;
use Motomedialab\Bunny\Data\ManageVideos\VideoData;
use Motomedialab\Connector\Contracts\RequestInterface;

/**
 * Get details of a specific video
 *
 * @implements RequestInterface<VideoData|ApiError>
 */
readonly class GetVideoRequest extends BaseRequest implements RequestInterface
{
    /**
     * @param  int  $libraryId  The library the video was uploaded to
     * @param  string  $videoId  The video ID
     */
    public function __construct(public int $libraryId, public string $videoId)
    {
        //
    }

    public function endpoint(): string
    {
        return "library/$this->libraryId/videos/$this->videoId";
    }

    public function toResponse(Response $response): ApiError|VideoData
    {
        return when(
            $response->ok(),
            fn (): VideoData => new VideoData($response->json()),
            fn (): ApiError => ApiError::fromResponse($response)
        );
    }
}
