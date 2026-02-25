<?php

namespace Vogelyt\AbsenceIoClient\Auth;

use Dflydev\Hawk\Client\Client;
use Dflydev\Hawk\Client\ClientBuilder;
use Dflydev\Hawk\Credentials\Credentials;

class HawkAuth
{
    private Client $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
    }

    public function sign(
        string $method,
        string $uri,
        string $hawkId,
        string $hawkKey,
        ?string $body = null,
        ?string $contentType = null
    ): array {

        $credentials = new Credentials($hawkKey, 'sha256', $hawkId);

        $request = $this->client->createRequest(
            $credentials,
            $uri,
            $method,
            [
                'payload' => $body,
                'content_type' => $contentType,
            ]
        );

        return [
            $request->header()->fieldName() => $request->header()->fieldValue()
        ];
    }
}