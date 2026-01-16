<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Jobs;

use Illuminate\Bus\Queueable;
use Motomedialab\Bunny\Data\ApiError;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Motomedialab\Bunny\Data\ManageVideos\UploadResponse;
use Motomedialab\Bunny\Events\VideoUploadFailed;
use Motomedialab\Bunny\Integrations\Connectors\BunnyStreamConnector;
use Motomedialab\Bunny\Integrations\Requests\ManageVideos\FetchVideoUrlRequest;

class UploadVideoFromUrl implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public int $libraryId,
        public string $url,
        public string $title,
        public array $metadata = [],
        public array $fetchHeaders = [],
        public ?int $collectionId = null,
    ) {
        //
    }

    /**
     * Allow up to three retries for this job.
     */
    public function tries(): int
    {
        return 3;
    }

    /**
     * Increase back-off duration from 1 minute, to 3 and finally to five.
     *
     * @return int[]
     */
    public function backoff(): array
    {
        return [60, 60 * 3, 60 * 5];
    }

    public function handle(BunnyStreamConnector $connector): int
    {
        $response = $connector->send(
            new FetchVideoUrlRequest(
                libraryId: $this->libraryId,
                url: $this->url,
                title: $this->title,
                fetchHeaders: $this->fetchHeaders,
                collectionId: $this->collectionId,
            )
        );

        if ($response instanceof ApiError) {
            return $this->handleError($response);
        }

        return $this->handleSuccess($response);
    }

    private function handleError(ApiError $response): int
    {
        event(new VideoUploadFailed($response, $this->metadata));
        return 1;
    }

    private function handleSuccess(UploadResponse $response): int
    {
        // we'll initially check for our transcoding status in two minutes time.
        dispatch(new CheckVideoTranscodingProgress($this->libraryId, $response->id(), $this->metadata))->delay(60 * 2);

        return 0;
    }
}
