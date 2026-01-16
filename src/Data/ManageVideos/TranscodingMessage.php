<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Data\ManageVideos;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Motomedialab\Bunny\Traits\HasData;
use Motomedialab\Bunny\Enums\TranscodingError;
use Motomedialab\Bunny\Enums\TranscodingMessageLevel;

final readonly class TranscodingMessage
{
    use HasData;

    public function timestamp(): CarbonInterface
    {
        return Carbon::make($this->get('timeStamp'));
    }

    public function level(): TranscodingMessageLevel
    {
        return TranscodingMessageLevel::from($this->get('level', 0));
    }

    public function issue(): ?TranscodingError
    {
        return TranscodingError::tryFrom($this->get('issueCode'));
    }

    public function message(): ?string
    {
        return $this->get('message');
    }

    public function value(): ?string
    {
        return $this->get('value');
    }
}
