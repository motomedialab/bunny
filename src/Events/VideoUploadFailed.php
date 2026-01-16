<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Events;

use Motomedialab\Bunny\Data\ApiError;
use Motomedialab\Bunny\Data\ManageVideos\VideoData;

readonly class VideoUploadFailed
{
    public function __construct(public ApiError|VideoData $error, public array $metadata)
    {
        //
    }
}
