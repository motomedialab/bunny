<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Data\ManageVideos;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Motomedialab\Bunny\Traits\HasData;
use Motomedialab\Bunny\Data\StorageSize;
use Motomedialab\Bunny\Enums\VideoStatus;
use Motomedialab\Bunny\Enums\VideoResolution;
use Motomedialab\Bunny\Enums\SmartGenerateStatus;

final readonly class VideoData
{
    use HasData;

    /**
     * @return int The ID of the video library that the video belongs to
     */
    public function videoLibraryId(): int
    {
        return $this->get('videoLibraryId');
    }

    /**
     * @return string The unique ID of the video
     */
    public function id(): string
    {
        return $this->get('guid');
    }

    /**
     * @return string The title of the video
     */
    public function title(): string
    {
        return $this->get('title');
    }

    /**
     * @return string|null The description of the video
     */
    public function description(): ?string
    {
        return $this->get('description');
    }

    /**
     * @return CarbonInterface|null The date when the video was uploaded
     */
    public function dateUploaded(): ?CarbonInterface
    {
        $timestamp = $this->get('dateUploaded');
        return null === $timestamp ? null : Carbon::make($timestamp);
    }

    /**
     * @return int The number of views the video received
     */
    public function views(): int
    {
        return $this->get('views', 0);
    }

    /**
     * @return bool Determines if the video is publically accessible
     */
    public function isPublic(): bool
    {
        return $this->get('isPublic', false);
    }

    /**
     * @return int The duration of the video in seconds
     */
    public function length(): int
    {
        return $this->get('length');
    }

    /**
     * @return VideoStatus The status of the video
     */
    public function videoStatus(): VideoStatus
    {
        return VideoStatus::from($this->get('status'));
    }

    /**
     * The framerate of the video
     */
    public function framerate(): float
    {
        return $this->get('framerate');
    }

    /**
     * @return int|null The rotation of the video
     */
    public function rotation(): ?int
    {
        return $this->get('rotation');
    }

    /**
     * @return int The width of the original video file
     */
    public function width(): int
    {
        return $this->get('width');
    }

    /**
     * @return int The height of the original video file
     */
    public function height(): int
    {
        return $this->get('height');
    }

    /**
     * @return VideoResolution[]|null[] Array of available video resolutions for this video
     */
    public function availableResolutions()
    {
        return array_map(
            fn (string $resolution): ?VideoResolution => VideoResolution::tryFrom(trim($resolution)),
            explode(',', $this->get('availableResolutions'))
        );
    }

    /**
     * @return int The number of thumbnails generated for this video
     */
    public function thumbnailCount(): int
    {
        return $this->get('thumbnailCount');
    }

    /**
     * @return int The current encode progress of the video as a percentage
     */
    public function encodeProgress(): int
    {
        return $this->get('encodeProgress', 0);
    }

    /**
     * @return StorageSize The amount of storage used by this video
     */
    public function storageSize(): StorageSize
    {
        return new StorageSize($this->get('storageSize', 0));
    }

    /**
     * @return bool Determines if the video has MP4 fallback files generated
     */
    public function hasMP4Fallback(): bool
    {
        return $this->get('hasMP4Fallback', false);
    }

    /**
     * @return string|null The ID of the collection where the video belongs
     */
    public function collectionId(): ?string
    {
        $collectionId = $this->get('collectionId');

        return empty($collectionId) ? null : $collectionId;
    }

    /**
     * @return int The average watch time of the video in seconds
     */
    public function averageWatchTime(): int
    {
        return $this->get('averageWatchTime', 0);
    }

    /**
     * @return int The total video watch time in seconds
     */
    public function totalWatchTime(): int
    {
        return $this->get('totalWatchTime', 0);
    }

    /**
     * @return string|null The automatically detected category of the video
     */
    public function category(): ?string
    {
        return $this->get('category');
    }

    /**
     * @return array<string, string> The list of meta tags that have been added to the video
     */
    public function metaTags(): array
    {
        return $this->get('metaTags', []);
    }

    /**
     * @return TranscodingMessage[] The list of transcoding messages that describe potential issues while the video was transcoding
     */
    public function transcodingMessages(): array
    {
        return array_map(fn (array $data) => new TranscodingMessage($data), $this->get('transcodingMessages', []));
    }

    /**
     * @return bool Whether just-in-time encoding is enabled for this video.
     */
    public function jitEncodingEnabled(): bool
    {
        return $this->get('jitEncodingEnabled', false);
    }

    /**
     * @return SmartGenerateStatus|null The status of smart generate action (if triggered)
     */
    public function smartGenerateStatus(): ?SmartGenerateStatus
    {
        return SmartGenerateStatus::tryFrom($this->get('smartGenerateStatus'));
    }

    /**
     * @return bool Determines if video has the original file available in storage
     */
    public function hasOriginal(): bool
    {
        return $this->get('hasOriginal', true);
    }

    /**
     * @return string|null Original uploaded file SHA256 hash
     */
    public function originalHash(): ?string
    {
        return $this->get('originalHash');
    }
}
