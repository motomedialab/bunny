<?php

namespace Motomedialab\Bunny\Integrations\Connectors;

use Illuminate\Http\Client\PendingRequest;
use Motomedialab\Connector\BaseConnector;

class BunnyStreamConnector extends BaseConnector
{
    public function __construct(private string $apiKey)
    {
        //
    }

    public function authenticateRequest(PendingRequest $request): PendingRequest
    {
        return $request->withHeader('AccessKey', $this->apiKey);
    }

    public function apiUrl(): string
    {
        return 'https://video.bunnycdn.com/';
    }
}
