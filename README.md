# MotoMediaLab BunnyCDN Integration

This is an expanding library of [bunny.net](https://bunny.net/) integrations developed by MotoMediaLab.

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
])
```

The above will dispatch a job to begin transcoding, which in turn
will dispatch check jobs to monitor the progress of the video upload.

### Events

```php
use Motomedialab\Bunny\Events;

# at every transcoding check, an event will be emitted
# so you can monitor the progress
Events\VideoTranscoding::class

# listen for transcoding finished - at this point the video is complete
Events\VideoTranscodingFinished::class;

# on transcoding failure, this event will be fired. You can get more information
# on the type of the failure via $event->video->transcodingMessages()
Events\VideoTranscodingFailed::class;

# on upload failure, this event will be dispatched along with metadata
# and where applicable the API error that occurred or the video object. 
Events\VideoUploadFailed::class;
```

### Metadata

For trackability, we recommend sending metadata. This is shared throughout the workflow
so you can react to it further down the process.

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
