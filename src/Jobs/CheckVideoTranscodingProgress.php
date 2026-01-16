<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Jobs;

use Motomedialab\Bunny\Data\ApiError;
use Illuminate\Queue\InteractsWithQueue;
use Motomedialab\Bunny\Enums\VideoStatus;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Motomedialab\Bunny\Events\VideoTranscoding;
use Motomedialab\Bunny\Events\VideoUploadFailed;
use Motomedialab\Bunny\Data\ManageVideos\VideoData;
use Motomedialab\Bunny\Events\VideoTranscodingFailed;
use Motomedialab\Bunny\Events\VideoTranscodingFinished;
use Motomedialab\Bunny\Integrations\Connectors\BunnyStreamConnector;
use Motomedialab\Bunny\Integrations\Requests\ManageVideos\GetVideoRequest;

/**
 * Dispatched after a video has been uploaded,
 * this job reports on the progress of a transcoding.
 */
class CheckVideoTranscodingProgress implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(public int $libraryId, public string $videoId, public array $metadata)
    {
        //
    }

    public function handle(BunnyStreamConnector $connector): int
    {
        $response = $connector->send(new GetVideoRequest($this->libraryId, $this->videoId));

        if ($response instanceof ApiError) {
            return $this->handleError($response);
        }

        return $this->handleSuccess($response);
    }

    private function handleError(ApiError $error): int
    {
        // ToDo: better handling of errors.

        return 1;
    }

    private function handleSuccess(VideoData $video): int
    {
        if ($video->videoStatus() === VideoStatus::Finished) {
            event(new VideoTranscodingFinished($video, $this->metadata));

            return 0;
        }

        if ($video->videoStatus() === VideoStatus::Error) {
            event(new VideoTranscodingFailed($video, $this->metadata));

            return 0;
        }

        if ($video->videoStatus() === VideoStatus::UploadFailed) {
            event(new VideoUploadFailed($video, $this->metadata));

            return 0;
        }

        // if we get here, we need to check again in 30 seconds time...
        dispatch(new CheckVideoTranscodingProgress($video->videoLibraryId(), $video->id(), $this->metadata))->delay(30);
        event(new VideoTranscoding($video, $this->metadata, $video->encodeProgress()));

        return 0;
    }
}
