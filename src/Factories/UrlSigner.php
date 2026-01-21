<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Factories;

class UrlSigner
{
    /**
     * @var array<string,string>
     */
    private array $queryParams = [];

    /**
     * @var string[]
     */
    private array $allowedCountries = [];

    /**
     * @var string[]
     */
    private array $blockedCountries = [];

    private ?string $ipAddress = null;

    private ?int $expiration = null;

    private ?string $tokenPath = null;

    public function __construct(
        private readonly string $secretKey,
        private readonly string $pzHostname
    ) {
        //
    }

    /**
     * @param array<string,string> $params
     */
    public function withQueryParams(array $params): UrlSigner
    {
        $this->queryParams = $params;

        return $this;
    }

    /**
     * @param string[] $countries Array of country codes, e.g. US, GB.
     */
    public function allowedCountries(array $countries): UrlSigner
    {
        $this->allowedCountries = $countries;

        return $this;
    }

    /**
     * @param string[] $countries Array of country codes, e.g. US, GB.
     */
    public function blockedCountries(array $countries): UrlSigner
    {
        $this->blockedCountries = $countries;

        return $this;
    }

    public function restrictToIp(?string $ipAddress): UrlSigner
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function expiresInSeconds(int $seconds): UrlSigner
    {
        $this->expiration = time() + $seconds;

        return $this;
    }

    public function expiresInMinutes(int $minutes): UrlSigner
    {
        return $this->expiresInSeconds($minutes * 60);
    }

    public function expiresInHours(int $hours): UrlSigner
    {
        return $this->expiresInMinutes($hours * 60);
    }

    public function tokenPath(string $path): UrlSigner
    {
        $this->tokenPath = str_starts_with($path, '/') ? $path : '/' . $path;

        return $this;
    }

    public function sign(string $path): string
    {
        $queryParams = $this->generateQueryParams();

        return 'https://'
            . $this->pzHostname
            . $path
            . (str_contains($path, '?') ? '&' : '?')
            . 'token=' . $this->buildSigningKey($path, $queryParams)
            . ($this->expiration ? '&expires=' . $this->expiration : '')
            . ($queryParams ? '&' . http_build_query($queryParams) : null);
    }

    /**
     * @param array<string,string> $queryParams
     */
    private function buildSigningKey(string $path, array $queryParams): string
    {
        $data = implode('', $this->collateData($path, $queryParams));
        $signature = hash('sha256', $data, true);
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    }

    /**
     * @param array<string,string> $queryParams
     * @return array<int, string|null>
     */
    private function collateData(string $path, array $queryParams): array
    {
        $path = str_starts_with($path, '/') ? $path : '/' . $path;
        $pairs = [];

        foreach ($queryParams as $k => $v) {
            $pairs[] = $k . '=' . $v;
        }

        return [$this->secretKey, $path, (string)$this->expiration, $this->ipAddress, implode('&', $pairs)];
    }

    /**
     * @return array<string,string>
     */
    private function generateQueryParams(): array
    {
        $queryParams = $this->queryParams;

        if (null !== $this->tokenPath) {
            $queryParams['token_path'] = $this->tokenPath;
        }

        if (count($this->allowedCountries) > 0) {
            $queryParams['token_countries'] = implode(',', $this->allowedCountries);
        }

        if (count($this->blockedCountries) > 0) {
            $queryParams['token_countries_blocked'] = implode(',', $this->blockedCountries);
        }

        ksort($queryParams);

        return $queryParams;
    }


}
