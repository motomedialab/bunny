<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Traits;

use Illuminate\Support\Arr;
use Illuminate\Http\Client\Response;

trait HasData
{
    public function __construct(public readonly array $data)
    {
        //
    }

    public function get(string $key, mixed $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    public static function fromResponse(Response $response): static
    {
        return new (static::class)($response->json());
    }
}
