<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Data\Payloads;

class UpdateVideo
{
    private array $data = [];

    public function title(?string $title): self
    {
        return $this->set('title', $title);
    }

    public function collectionId(?int $collectionId): self
    {
        return $this->set('collectionId', $collectionId);
    }

    public function metaTags(array $metaTags): self
    {
        return $this->set('metaTags', $metaTags);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    private function set(string $key, mixed $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }
}
