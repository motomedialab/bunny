<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Data\ManageCollections;

use Motomedialab\Bunny\Traits\HasData;
use Motomedialab\Bunny\Data\StorageSize;

final readonly class CollectionData
{
    use HasData;

    /**
     * @return int The video library ID that contains the collection
     */
    public function videoLibraryId(): int
    {
        return $this->get('videoLibraryId');
    }

    /**
     * @return string|null The unique ID of the collection
     */
    public function guid(): ?string
    {
        return $this->get('guid');
    }

    /**
     * @return string|null The name of the collection
     */
    public function name(): ?string
    {
        return $this->get('name');
    }

    /**
     * @return int The number of videos that the collection contains
     */
    public function videoCount(): int
    {
        return $this->get('videoCount', 0);
    }

    /**
     * @return StorageSize The total storage size of the collection
     */
    public function totalSize(): StorageSize
    {
        return new StorageSize($this->get('totalSize', 0));
    }

    /**
     * @return string|null The IDs of videos to be used as preview icons
     */
    public function previewVideoIds(): ?string
    {
        return $this->get('previewVideoIds');
    }

    /**
     * @return array The URLs of preview images of videos in the collection
     */
    public function previewImageUrls(): array
    {
        return $this->get('previewImageUrls', []);
    }
}
