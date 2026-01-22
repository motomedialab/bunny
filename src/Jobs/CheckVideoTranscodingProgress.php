<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Jobs;

use Motomedialab\Bunny\Data\ApiError;
use Illuminate\Queue\InteractsWithQueue;
use Motomedialab\Bunny\Enums\VideoStatus;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Motomedialab\Bunny\Events\VideoTranscoding;
use Motomedialab\Bunny\Exceptions\JobException;
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

    /**
     * Allow this job to process until its timeout
     */
    public function tries(): int
    {
        return 0;
    }

    /**
     * Fail if the job hasn't completed in 30 minutes
     */
    public function timeout(): int
    {
        return 60 * 30;
    }

    /**
     * Define the maximum number of unhandled exceptions we'll allow.
     */
    public function maxExceptions(): int
    {
        return 3;
    }

    public function handle(BunnyStreamConnector $connector): void
    {
        $response = $connector->send(new GetVideoRequest($this->libraryId, $this->videoId));

        if ($response instanceof ApiError) {
            throw JobException::fromApiError($response);
        }

        $this->checkStatus($response);
    }

    public function failed(\Throwable $exception): void
    {
        event(new VideoTranscodingFailed(null, $this->metadata));
    }

    private function checkStatus(VideoData $video): void
    {
        if ($video->videoStatus() === VideoStatus::Finished) {
            event(new VideoTranscodingFinished($video, $this->metadata));
            return;
        }

        if ($video->videoStatus() === VideoStatus::Error) {
            event(new VideoTranscodingFailed($video, $this->metadata));
            return;
        }

        if ($video->videoStatus() === VideoStatus::UploadFailed) {
            event(new VideoUploadFailed($video, $this->metadata));
            return;
        }

        // if we get here, let our system know its still going and release the job
        event(new VideoTranscoding($video, $this->metadata, $video->encodeProgress()));
        $this->release(30);
    }
}
