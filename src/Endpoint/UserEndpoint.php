<?php

namespace Vogelyt\AbsenceIoClient\Endpoint;

use Vogelyt\AbsenceIoClient\Http\HttpClient;

class UserEndpoint
{
    public function __construct(
        private HttpClient $http
    ) {}

    public function getAll(): array
    {
        return $this->http->get('users');
    }
}