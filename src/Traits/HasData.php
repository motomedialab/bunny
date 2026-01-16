<?php

namespace Motomedialab\Bunny\Traits;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

trait HasData
{
    public function __construct(public readonly array $data)
    {
        //
    }

    public function get(string $key)
    {
        return Arr::get($this->data, $key);
    }

    public static function fromResponse(Response $response): static
    {
        return new (static::class)($response->json());
    }
}
