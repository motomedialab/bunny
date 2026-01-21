<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Integrations\Requests\ManageVideos;

use Illuminate\Http\Client\Response;
use Motomedialab\Bunny\Data\ApiError;
use Motomedialab\Connector\BaseRequest;
use Motomedialab\Connector\Enums\RequestMethod;
use Motomedialab\Connector\Contracts\RequestInterface;
use Motomedialab\Bunny\Data\ManageVideos\UploadResponse;

/**
 * Upload a video via a URL
 *
 * @implements RequestInterface<UploadResponse|ApiError>
 *
 * @see https://docs.bunny.net/reference/video_fetchnewvideo
 */
readonly class FetchVideoUrlRequest extends BaseRequest implements RequestInterface
{
    /**
     * @param  int  $libraryId  The Library that the video should be uploaded to
     * @param  string  $url  The URL the video should be fetched from
     * @param  ?string  $title  The title of the video
     * @param  array<string, string>  $fetchHeaders  An array of headers to send with the fetch request.
     * @param  ?string  $collectionId  Optional collection ID to upload the video into
     * @param  ?int  $thumbnailTimeMs  The time in milliseconds to extract the thumbnail frame
     */
    public function __construct(
        public int $libraryId,
        public string $url,
        public ?string $title = null,
        public array $fetchHeaders = [],
        public ?string $collectionId = null,
        public ?int $thumbnailTimeMs = null,
    ) {
        //
    }

    public function method(): RequestMethod
    {
        return RequestMethod::POST;
    }

    public function endpoint(): string
    {
        return "library/{$this->libraryId}/videos/fetch";
    }

    /**
     * @return array<string, mixed>
     */
    public function body(): array
    {
        return array_filter([
            'url' => $this->url,
            'headers' => count($this->fetchHeaders) ? $this->fetchHeaders : null,
            'title' => $this->title,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function queryParams(): array
    {
        return array_filter([
            'collectionId' => $this->collectionId,
            'thumbnailTimeMs' => $this->thumbnailTimeMs,
        ]);
    }

    public function toResponse(Response $response): UploadResponse|ApiError
    {
        return $response->json('success') === true
            ? UploadResponse::fromResponse($response)
            : ApiError::fromResponse($response);
    }
}
