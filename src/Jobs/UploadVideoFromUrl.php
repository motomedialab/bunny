<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Jobs;

use Closure;
use Illuminate\Bus\Queueable;
use Motomedialab\Bunny\Data\ApiError;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Motomedialab\Bunny\Exceptions\JobException;
use Motomedialab\Bunny\Events\VideoUploadFailed;
use Laravel\SerializableClosure\SerializableClosure;
use Motomedialab\Bunny\Data\ManageVideos\UploadResponse;
use Motomedialab\Bunny\Integrations\Connectors\BunnyStreamConnector;
use Motomedialab\Bunny\Integrations\Requests\ManageVideos\FetchVideoUrlRequest;

class UploadVideoFromUrl implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    public string|SerializableClosure $url;


    /**
     * @param int $libraryId
     * @param string $url
     * @param string $title
     * @param array<string, mixed> $metadata
     * @param array<string, string> $fetchHeaders
     * @param string|null $collectionId
     */
    public function __construct(
        public int $libraryId,
        string|Closure $url,
        public string $title,
        public array $metadata = [],
        public array $fetchHeaders = [],
        public ?string $collectionId = null,
    ) {
        // allow passing of a closure to this method
        $this->url = $url instanceof Closure ? new SerializableClosure($url) : $url;
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

    public function handle(BunnyStreamConnector $connector): void
    {
        $url = is_callable($this->url) ? ($this->url)() : $this->url;

        $response = $connector->send(
            new FetchVideoUrlRequest(
                libraryId: $this->libraryId,
                url: (string) $url,
                title: $this->title,
                fetchHeaders: $this->fetchHeaders,
                collectionId: $this->collectionId,
            )
        );

        if ($response instanceof ApiError) {
            throw JobException::fromApiError($response);
        }

        $this->handleSuccess($response);
    }

    /**
     * Laravel will call on this on failure.
     */
    public function failed(\Throwable $exception): int
    {
        if ($exception instanceof JobException) {
            $apiError = $exception->apiError;
        } else {
            $apiError = new ApiError(
                errorKey: 'unknown',
                errorMessage: $exception->getMessage(),
                statusCode: $exception->getCode()
            );
        }

        event(new VideoUploadFailed($apiError, $this->metadata));
        return 1;
    }

    private function handleSuccess(UploadResponse $response): void
    {
        if (is_null($response->id())) {
            throw new \Exception('Failed to get video ID');
        }

        dispatch(
            new CheckVideoTranscodingProgress(
                $this->libraryId,
                $response->id(),
                $this->metadata
            )
        )->delay(60 * 2);
    }
}
