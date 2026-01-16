<?php

return [

    /**
     * Bunny stream uses separate API endpoints to other elements.
     */
    'stream' => [
        /**
         * Stream Video library ID
         * Find it: Stream > Library > API > API access information Video Library ID
         */
        'video_library_id' => env('BUNNY_STREAM_LIBRARY_ID'),

        /**
         * Stream Hostname
         * Find it: Stream > Library > API > API access information > CDN hostname
         */
        'cdn_hostname' => env('BUNNY_STREAM_HOSTNAME'),

        /**
         * Stream Library API Key
         * Find it: Stream > Library > API > API Key
         */
        'api_key' => env('BUNNY_STREAM_API_KEY'),
    ],
];
