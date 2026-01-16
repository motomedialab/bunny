<?php

namespace Motomedialab\Bunny\Data;

use Illuminate\Http\Client\Response;

final class ApiError
{
    public function __construct(public string $errorKey, public string $errorMessage, public ?string $field = null)
    {
        //
    }

    public static function fromResponse(Response $response): self
    {
        if ($response->badRequest()) {
            return new self(
                $response->json('ErrorKey'),
                $response->json('Message'),
                $response->json('Field'),
            );
        }

        if ($response->unauthorized()) {
            return new self(
                'unauthorized',
                'Server returned a 401 unauthorized',
            );
        }

        if ($response->notFound()) {
            return new self(
                'notFound',
                'The requested resource was not found',
            );
        }

        return new self(
            'serverError',
            'The remote server encountered an error. Status code: '.$response->status(),
        );
    }
}
