<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Data;

use Illuminate\Http\Client\Response;

final class ApiError
{
    public function __construct(
        public string $errorKey,
        public string $errorMessage,
        public int $statusCode,
        public ?string $field = null
    ) {
        //
    }

    public static function fromResponse(Response $response): self
    {
        if ($response->badRequest()) {
            return new self(
                $response->json('ErrorKey'),
                $response->json('Message'),
                $response->status(),
                $response->json('Field'),
            );
        }

        if ($response->unauthorized()) {
            return new self(
                'unauthorized',
                'Server returned a 401 unauthorized',
                $response->status(),
            );
        }

        if ($response->notFound()) {
            return new self(
                'notFound',
                'The requested resource was not found',
                $response->status(),
            );
        }

        return new self(
            'serverError',
            'The remote server encountered an error.',
            $response->status(),
        );
    }
}
