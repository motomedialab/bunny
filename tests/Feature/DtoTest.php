<?php

declare(strict_types=1);

use Carbon\CarbonInterface;
use Motomedialab\Bunny\Data\ManageVideos\TranscodingMessage;
use Motomedialab\Bunny\Data\StorageSize;
use Motomedialab\Bunny\Enums\TranscodingError;
use Motomedialab\Bunny\Enums\VideoStatus;
use Motomedialab\Bunny\Enums\VideoResolution;
use Motomedialab\Bunny\Data\ManageVideos\VideoData;

it('can create a video details dto', function () {
    $data = json_decode(
        '{"videoLibraryId":568241,"guid":"1e246d41-b219-41a5-be0e-e71b314e06b7","title":"A test video goes here","description":null,"dateUploaded":"2026-01-14T17:26:41.697","views":0,"isPublic":false,"length":5,"status":4,"framerate":30,"rotation":0,"width":1920,"height":1080,"availableResolutions":"480p","outputCodecs":"x264","thumbnailCount":6,"encodeProgress":100,"storageSize":10754729,"captions":[],"hasMP4Fallback":true,"collectionId":"","thumbnailFileName":"thumbnail.jpg","thumbnailBlurhash":null,"averageWatchTime":0,"totalWatchTime":0,"category":"gaming","chapters":[],"moments":[],"metaTags":[],"transcodingMessages":[],"jitEncodingEnabled":false,"smartGenerateStatus":null,"hasOriginal":true,"originalHash":"05BD857AF7F70BF51B6AAC1144046973BF3325C9101A554BC27DC9607DBBD8F5"}',
        true
    );

    $dto = new VideoData($data);

    expect($dto)
        ->videoLibraryId()->toBe(568241)
        ->id()->toBe('1e246d41-b219-41a5-be0e-e71b314e06b7')
        ->title()->toBe('A test video goes here')
        ->description()->toBeNull()
        ->dateUploaded()->toBeInstanceOf(Carbon\CarbonInterface::class)
        ->views()->toBe(0)
        ->isPublic()->toBe(false)
        ->length()->toBe(5)
        ->videoStatus()->toBe(VideoStatus::Finished)
        ->framerate()->toBe(30.0)
        ->rotation()->toBe(0)
        ->width()->toBe(1920)
        ->height()->toBe(1080)
        ->availableResolutions()->toBeArray()->toHaveCount(1)
        ->thumbnailCount()->toBe(6)
        ->encodeProgress()->toBe(100)
        ->storageSize()->toBeInstanceOf(StorageSize::class)
        ->storageSize()->toString(1)->tobe('10.3 MB')
        ->hasMP4Fallback()->toBe(true)
        ->collectionId()->toBeNull()
        ->averageWatchTime()->toBe(0)
        ->totalWatchTime()->toBe(0)
        ->category()->toBe('gaming')
        ->metaTags()->toBeArray()->toHaveCount(0)
        ->hasOriginal()->toBe(true)
        ->originalHash()->toBe('05BD857AF7F70BF51B6AAC1144046973BF3325C9101A554BC27DC9607DBBD8F5')
        ->and($dto->availableResolutions()[0])
        ->toBeInstanceOf(VideoResolution::class)
        ->toBe(VideoResolution::Res480);
});

it('can create a transcoding message', function () {
    $message = new TranscodingMessage([
        'timeStamp' => '2025-01-01T15:12+00:00',
        'level' => 3,
        'issueCode' => 5,
        'message' => 'A message here',
        'value' => 'A value here'
    ]);

    expect($message)
        ->timestamp()->toBeInstanceOf(CarbonInterface::class)
        ->issue()->toBe(TranscodingError::VideoExceededMaxDuration)
        ->level()->toBe(\Motomedialab\Bunny\Enums\TranscodingMessageLevel::Error)
        ->message()->toBe('A message here')
        ->value()->toBe('A value here');
});
