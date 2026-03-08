<?php

namespace Vogelyt\AbsenceIoClient\Endpoint;

use Vogelyt\AbsenceIoClient\Http\HttpClient;

class UserEndpoint
{
    public function __construct(
        private HttpClient $http
    ) {}
    // retrieve all users
    public function getAll(): array
    {
        // Users have to be fetched with empty payload and post method
        return $this->http->post('users', []);
    }
}