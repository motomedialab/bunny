<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Exceptions;

use Motomedialab\Bunny\Data\ApiError;

class JobException extends \Exception
{
    public ApiError $apiError;

    public function apiError(ApiError $apiError): JobException
    {
        $this->apiError = $apiError;

        return $this;
    }

    public static function fromApiError(ApiError $apiError): JobException
    {
        return (new JobException($apiError->errorMessage, $apiError->statusCode))
            ->apiError($apiError);
    }
}
