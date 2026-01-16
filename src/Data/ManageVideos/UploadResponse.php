<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Data\ManageVideos;

use Motomedialab\Bunny\Traits\HasData;

final readonly class UploadResponse
{
    use HasData;

    public function success(): bool
    {
        return $this->get('success');
    }

    public function id(): ?string
    {
        return $this->get('id');
    }

    public function message(): ?string
    {
        return $this->get('message');
    }
}
