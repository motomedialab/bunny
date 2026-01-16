<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Events;

use Illuminate\Support\Arr;
use Motomedialab\Bunny\Data\ManageVideos\VideoData;

abstract readonly class VideoTranscodingEvent
{
    public function __construct(public VideoData $video, public array $metadata)
    {
        //
    }

    public function metadata($dot = ''): mixed
    {
        return Arr::get($this->metadata, $dot);
    }
}
