<?php

namespace Motomedialab\Bunny\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class UploadVideoFromUrl implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $url, public array $metadata = [])
    {
        //
    }

    public function handle(): void
    {

    }


}