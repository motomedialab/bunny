<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Actions;

use Closure;
use Motomedialab\Bunny\Jobs\UploadVideoFromUrl as UploadVideoJob;

class UploadVideoFromUrl
{
    /**
     * @param  string|Closure  $url  Absolute URL to video to upload or closure resolving to URL
     * @param  string  $title  Title of the video
     * @param  array<string, mixed>  $metadata  Job metadata - will be distributed throughout jobs
     * @param  array<string, string>  $fetchHeaders  Headers to use when fetching video
     * @param  string|null  $collectionId  Optional ID of the collection to add to
     */
    public function __invoke(
        string|Closure $url,
        string $title,
        array $metadata = [],
        array $fetchHeaders = [],
        ?string $collectionId = null
    ): void {
        $libraryId = (int) config('bunny.stream.video_library_id');

        dispatch(new UploadVideoJob($libraryId, $url, $title, $metadata, $fetchHeaders, $collectionId));
    }
}
