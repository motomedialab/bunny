<?php

declare(strict_types=1);

use Motomedialab\Bunny\Factories\UrlSigner;

it('can sign a url', function () {

    $hostname = 'a-test-pz.b-cdn.net';
    $signingKey = 'c7f105d6-530f-4437-9463-82995994c67a';
    $path = '/p/299/responsive-images/ISSPA3_0003_12___image_516_516.jpeg';

    $url = (new UrlSigner($signingKey, $hostname))
        ->restrictToIp('10.10.10.10')
        ->blockedCountries(['US', 'IT'])
        ->allowedCountries(['GB'])
        ->expiresInMinutes(60)
        ->withQueryParams(['width' => 1920, 'height' => 1080])
        ->tokenPath('/p')
        ->sign($path);

    $result = parse_url($url);

    expect($result['host'])
        ->toBe('a-test-pz.b-cdn.net')
        ->and($result['scheme'])->toBe('https')
        ->and($result['path'])->toBe('/p/299/responsive-images/ISSPA3_0003_12___image_516_516.jpeg')
        ->and($result['query'])
        ->toBeString()
        ->toContain('token=', 'token_countries=GB', 'token_countries_blocked=US%2CIT', 'height=1080', 'width=1920');
});
