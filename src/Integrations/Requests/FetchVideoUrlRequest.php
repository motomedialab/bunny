<?php

namespace Motomedialab\Bunny\Integrations\Requests;

use Illuminate\Http\Client\Response;
use Motomedialab\Bunny\Data\UploadResponse;
use Motomedialab\Connector\BaseRequest;
use Motomedialab\Connector\Contracts\RequestInterface;

/**
 * @implements RequestInterface<UploadResponse>
 */
final readonly class FetchVideoUrlRequest extends BaseRequest implements RequestInterface
{
    public function __construct(public int $libraryId, public string $url, public ?string $title = null)
    {
        //
    }

    public function endpoint(): string
    {
        return "library/{$this->libraryId}/videos/fetch";
    }

    public function body(): array
    {
        return [
            'url' => $this->url,
            'title' => $this->title,
        ];
    }

    public function toResponse(Response $response): UploadResponse
    {
        return $response->json('success') === true
            ? new UploadResponse(true, $response->json('id'))
            : new UploadResponse(false, message: $response->json('message', 'Status code: ' . $response->status()));
    }
}