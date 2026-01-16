<?php

use Illuminate\Support\Facades\Http;
use Motomedialab\Bunny\Data\ManageVideos\UploadResponse;
use Motomedialab\Bunny\Integrations\Connectors\BunnyStreamConnector;
use Motomedialab\Bunny\Integrations\Requests\ManageVideos\FetchVideoUrlRequest;

describe('configuration checks', function () {
    it('can get the video library id', function () {
        expect(config('bunny.stream.video_library_id'))->toBe('12345');
    });

    it('can get the api key', function () {
        expect(config('bunny.stream.api_key'))->toBe('testApiKey');
    });

    it('can get the cdn hostname', function () {
        expect(config('bunny.stream.cdn_hostname'))->toBe('vz-9bas219-81a.b-cdn.net');
    });
});

describe('test connector', function () {
    it('can instantiate connector', function () {
        $connector = app(BunnyStreamConnector::class);
        expect($connector)->toBeInstanceOf(BunnyStreamConnector::class);
    });
});

describe('upload video tests', function () {
    it('can upload a video using fetch', function () {
        Http::fake([
            'https://video.bunnycdn.com/library/12345/videos/fetch' => Http::response([
                'id' => '1e246d41-b219-41a5-be0e-e71b314e06b7',
                'success' => true,
                'message' => 'OK',
                'statusCode' => 200,
            ]),
        ]);

        $request = new FetchVideoUrlRequest(
            config('bunny.stream.video_library_id'),
            'https://google.co.uk',

        );
        $response = app(BunnyStreamConnector::class)->send($request);

        expect($response)->toBeInstanceOf(UploadResponse::class)
            ->id()->toBeString()->toBe('1e246d41-b219-41a5-be0e-e71b314e06b7')
            ->success()->toBeTrue();
    });
});
