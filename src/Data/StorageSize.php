<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Data;

final readonly class StorageSize
{
    public function __construct(public int $bytes)
    {
        //
    }

    public function toString(int $decimals = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $value = $this->bytes;
        $i = 0;

        while ($value >= 1024 && $i < count($units) - 1) {
            $value /= 1024;
            $i++;
        }

        return number_format($value, $decimals).' '.$units[$i];
    }
}
