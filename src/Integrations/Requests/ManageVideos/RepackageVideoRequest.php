<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Integrations\Requests\ManageVideos;

use Motomedialab\Bunny\Data\ApiError;
use Motomedialab\Connector\BaseRequest;
use Motomedialab\Connector\Enums\RequestMethod;
use Motomedialab\Connector\Contracts\RequestInterface;

/**
 * @implements RequestInterface<true|ApiError>
 */
readonly class RepackageVideoRequest extends BaseRequest implements RequestInterface
{
    use IsVideoRequest {
        IsVideoRequest::endpoint as traitEndpoint;
    }

    public function method(): RequestMethod
    {
        return RequestMethod::POST;
    }

    public function endpoint(): string
    {
        return $this->traitEndpoint() . '/repackage';
    }
}
