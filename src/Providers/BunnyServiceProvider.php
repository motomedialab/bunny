<?php

namespace Motomedialab\Bunny\Providers;


use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Motomedialab\Bunny\Integrations\BunnyStreamConnector;

class BunnyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/bunny-stream.php', 'bunny-stream');

        app()->scoped(BunnyStreamConnector::class, function (Application $app) {
            return new BunnyStreamConnector(
                Arr::get($app, 'config.bunny-stream.api_key'),
                Arr::get($app, 'config.bunny-stream.video_library_id')
            );
        });
    }
}