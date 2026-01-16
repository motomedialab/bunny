# MotoMediaLab BunnyCDN Integration

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/motomedialab/bunny.svg?style=flat-square)](https://packagist.org/packages/motomedialab/bunny)
[![Total Downloads](https://img.shields.io/packagist/dt/motomedialab/bunny.svg?style=flat-square)](https://packagist.org/packages/motomedialab/bunny)
[![Tests](https://github.com/motomedialab/bunny/actions/workflows/tests.yml/badge.svg)](https://github.com/motomedialab/bunny/actions/workflows/tests.yml)

This is an expanding library of [bunny.net](https://bunny.net/) integrations developed by MotoMediaLab.
Currently, it only supports uploading of videos via the Bunny Stream fetch method and some basic integration
around this. As our requirements expand, this package will be updated with new functionality.

## Installation

Installation can be achieved quickly and easily using Composer:

```bash
composer require motomedialab/bunny
```

## Configuration

Below are the configuration parameters for Bunny Stream:

```dotenv
# library ID that videos should be uploaded to
BUNNY_STREAM_LIBRARY_ID=12345

# the CDN hostname for your stream configuration
BUNNY_STREAM_HOSTNAME=vz-xyz.b-cdn.net

# the API key for bunny stream (this is different from your account API key!)
BUNNY_STREAM_API_KEY=YOUR_API_KEY
```

## Uploading a video

This library consolidates the process of uploading a video into a series of jobs.
To upload a video, you need the URL of the video location and a title at a minimum.

```php
// invokable class to trigger an upload job
app()->call(\Motomedialab\Bunny\Actions\UploadVideoFromUrl::class, [
    'url' => 'https://example.com/file.mp4',
    'title' => 'The video title'
]);
```

### The Upload Flow

The upload process is designed to be entirely asynchronous, leveraging Laravel's queueing system.
When you call the `UploadVideoFromUrl` invokable action, the following steps are initiated:

1. An `UploadVideoFromUrl` job is dispatched to the queue.
2. When the job is processed, it makes an API call to Bunny.net, instructing it to fetch the video from the specified URL.
3. If the API call fails for any reason, a `VideoUploadFailed` event is dispatched and the process ends.
4. On success, a `CheckVideoTranscodingProgress` job is dispatched to the queue with a 2-minute delay. This is to give Bunny.net time to start the transcoding process.
5. The `CheckVideoTranscodingProgress` job will then:
    - Fire a `VideoTranscoding` event with the current progress of the transcoding.
    - If the transcoding is finished, a `VideoTranscodingFinished` event is fired.
    - If the transcoding fails, a `VideoTranscodingFailed` event is fired.
    - If the initial upload failed, a `VideoUploadFailed` event is fired.
    - If the video is still transcoding, the job will re-dispatch itself with a 30-second delay.

This cycle will continue until the video has finished transcoding or an error occurs.

### Events

This package fires several events throughout the upload and transcoding process, allowing you to hook into the workflow and react accordingly.

---

#### `Motomedialab\Bunny\Events\VideoTranscoding`

This event is fired each time the `CheckVideoTranscodingProgress` job runs and the video is still transcoding. It provides the current progress of the transcoding, allowing you to display a progress bar or other feedback to the user.

---

#### `Motomedialab\Bunny\Events\VideoTranscodingFinished`

Once the video has been successfully transcoded, this event is fired. This is the point where you would typically update your database, associate the video with a model, and make it available to your users.

---

#### `Motomedialab\Bunny\Events\VideoTranscodingFailed`

If the transcoding process fails for any reason, this event will be fired. You can inspect the `$event->video->transcodingMessages()` to get more information on the type of failure.

---

#### `Motomedialab\Bunny\Events\VideoUploadFailed`

This event is dispatched if the initial upload fails. This can happen for a variety of reasons, such as an invalid URL or an API error. The event will contain the API error or video object where applicable, along with any metadata you passed.


### Metadata

To track your conversion job throughout the process, we recommend sending metadata. This is shared throughout the workflow
so you can react to the events later on.

```php
// dispatch job with metadata
app()->call(\Motomedialab\Bunny\Actions\UploadVideoFromUrl::class, [
    'url' => 'https://example.com/file.mp4',
    'title' => 'The video title',
    'metadata' => [
        'model' => 'App\Models\Post',
        'model_id' => 12,
    ]
]);

// listen for the transcoding finished event
class VideoTranscodingListener
{
    public function __construct(\Motomedialab\Bunny\Events\VideoTranscodingFinished $event)
    {
        if ($event->metadata('model') === 'App\Models\Post') {
            // ToDo: find your post and assign the video...
        }
    }
}
```
## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.