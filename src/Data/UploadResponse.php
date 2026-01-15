<?php

namespace Motomedialab\Bunny\Data;

final readonly class UploadResponse
{
    public function __construct(private bool $success, private ?string $videoId = null, private ?string $message = null)
    {
        //
    }

    public function success(): bool
    {
        return $this->success;
    }

    public function id(): ?string
    {
        return $this->videoId;
    }

    public function errorMessage(): ?string
    {
        return $this->message;
    }
}