<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Actions;

use Motomedialab\Bunny\Data\ApiError;
use Motomedialab\Bunny\Integrations\Connectors\BunnyStreamConnector;
use Motomedialab\Bunny\Integrations\Requests\ManageVideos\DeleteVideoRequest;

class DeleteVideo
{
    public function __construct(public BunnyStreamConnector $connector)
    {
        //
    }

    public function __invoke(string $videoId): true|ApiError
    {
        $libraryId = (int) config('bunny.stream.video_library_id');

        return $this->connector->send(new DeleteVideoRequest($libraryId, $videoId));
    }
}
