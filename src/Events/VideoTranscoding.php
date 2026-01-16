<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Events;

use Motomedialab\Bunny\Data\ManageVideos\VideoData;

readonly class VideoTranscoding extends VideoTranscodingEvent
{
    public function __construct(VideoData $video, array $metadata, public int $progress)
    {
        parent::__construct($video, $metadata);
    }
}
