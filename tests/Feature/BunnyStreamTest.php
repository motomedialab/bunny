<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Motomedialab\Bunny\Data\ApiError;
use Motomedialab\Bunny\Jobs\UploadVideoFromUrl;
use Motomedialab\Bunny\Data\ManageVideos\VideoData;
use Motomedialab\Bunny\Data\ManageVideos\UploadResponse;
use Motomedialab\Bunny\Jobs\CheckVideoTranscodingProgress;
use Motomedialab\Bunny\Integrations\Connectors\BunnyStreamConnector;
use Motomedialab\Bunny\Integrations\Requests\ManageVideos\GetVideoRequest;
use Motomedialab\Bunny\Integrations\Requests\ManageVideos\FetchVideoUrlRequest;

beforeEach(function () {
    Http::preventStrayRequests();

    $this->connector = app(BunnyStreamConnector::class);
});

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

it('can instantiate connector', function () {
    expect($this->connector)->toBeInstanceOf(BunnyStreamConnector::class);
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

describe('video interaction tests', function () {
    it('will return an api error on failure', function () {
        Http::fake(['*' => Http::response([], 401)]);

        $response = $this->connector->send(new GetVideoRequest(2345, 'test'));

        expect($response)->toBeInstanceOf(ApiError::class)
            ->errorKey->toBe('unauthorized');
    });

    it('can get a videos details', function () {
        Http::fake([
            'library/568241/videos/1e246d41-b219-41a5-be0e-e71b314e06b7' => Http::response(
                '{"videoLibraryId":568241,"guid":"1e246d41-b219-41a5-be0e-e71b314e06b7","title":"A test video goes here","description":null,"dateUploaded":"2026-01-14T17:26:41.697","views":0,"isPublic":false,"length":5,"status":4,"framerate":30,"rotation":0,"width":1920,"height":1080,"availableResolutions":"480p","outputCodecs":"x264","thumbnailCount":6,"encodeProgress":100,"storageSize":10754729,"captions":[],"hasMP4Fallback":true,"collectionId":"","thumbnailFileName":"thumbnail.jpg","thumbnailBlurhash":null,"averageWatchTime":0,"totalWatchTime":0,"category":"gaming","chapters":[],"moments":[],"metaTags":[],"transcodingMessages":[],"jitEncodingEnabled":false,"smartGenerateStatus":null,"hasOriginal":true,"originalHash":"05BD857AF7F70BF51B6AAC1144046973BF3325C9101A554BC27DC9607DBBD8F5"}'
            ),
        ]);

        $response = $this->connector->send(new GetVideoRequest(568241, '1e246d41-b219-41a5-be0e-e71b314e06b7'));

        expect($response)->toBeInstanceOf(VideoData::class)
            ->videoLibraryId()->toBe(568241)
            ->id()->toBe('1e246d41-b219-41a5-be0e-e71b314e06b7');
    });
});

describe('job tests', function () {
    it('can create a video transcoding request', function () {
        Bus::fake([CheckVideoTranscodingProgress::class]);

        Http::fake([
            'library/12345/videos/fetch' => Http::response([
                'id' => '1e246d41-b219-41a5-be0e-e71b314e06b7',
                'success' => true,
                'message' => 'OK',
                'statusCode' => 200,
            ]),
        ]);

        dispatch_sync(new UploadVideoFromUrl(12345, 'https://example.com/file.mp4', 'A video title', [
            'test' => true,
        ]));

        Bus::assertDispatchedTimes(function (CheckVideoTranscodingProgress $job) {
            return expect($job)
                ->libraryId->tobe(12345)
                ->metadata->toBe(['test' => true])
                ->videoId->toBe('1e246d41-b219-41a5-be0e-e71b314e06b7');
        });
    });
});
