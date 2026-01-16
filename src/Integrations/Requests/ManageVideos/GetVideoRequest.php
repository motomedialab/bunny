<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Integrations\Requests\ManageVideos;

use Motomedialab\Bunny\Data\ApiError;
use Motomedialab\Bunny\Data\ManageVideos\VideoData;
use Motomedialab\Bunny\Integrations\Requests\Traits\IsVideoRequest;
use Motomedialab\Connector\BaseRequest;
use Motomedialab\Connector\Contracts\RequestInterface;

/**
 * Get details of a specific video
 *
 * @implements RequestInterface<VideoData|ApiError>
 * @see https://docs.bunny.net/reference/video_getvideo
 */
readonly class GetVideoRequest extends BaseRequest implements RequestInterface
{
    use IsVideoRequest;
}
