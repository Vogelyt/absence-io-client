<?php

namespace Vogelyt\AbsenceIoClient;

use Vogelyt\AbsenceIoClient\Config\Config;
use Vogelyt\AbsenceIoClient\Http\HttpClient;
use Vogelyt\AbsenceIoClient\Endpoint\UserEndpoint;

class AbsenceClient
{
    private HttpClient $http;

    public function __construct(Config $config)
    {
        $this->http = new HttpClient($config);
    }

    public function users(): UserEndpoint
    {
        return new UserEndpoint($this->http);
    }
}