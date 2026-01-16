<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Actions;

use Motomedialab\Bunny\Jobs\UploadVideoFromUrl as UploadVideoJob;

class UploadVideoFromUrl
{
    /**
     * @param  string  $url  Absolute URL to video to upload
     * @param  string  $title  Title of the video
     * @param  array  $metadata  Job metadata - will be distributed throughout jobs
     * @param  array  $fetchHeaders  Headers to use when fetching video
     * @param  string|null  $collectionId  Optional ID of the collection to add to
     */
    public function __invoke(
        string $url,
        string $title,
        array $metadata = [],
        array $fetchHeaders = [],
        ?string $collectionId = null
    ): void {
        $libraryId = config('bunny.stream.video_library_id');

        dispatch(new UploadVideoJob($libraryId, $url, $title, $metadata, $fetchHeaders, $collectionId));
    }
}
