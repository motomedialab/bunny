<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Integrations\Connectors;

use Motomedialab\Connector\BaseConnector;
use Illuminate\Http\Client\PendingRequest;

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
