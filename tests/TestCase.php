<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Tests;

use Motomedialab\Bunny\Providers\BunnyServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            BunnyServiceProvider::class,
        ];
    }
}
