<?php

namespace Vogelyt\AbsenceIoClient;

use Vogelyt\AbsenceIoClient\Config\Config;
use Vogelyt\AbsenceIoClient\Http\HttpClient;
use Vogelyt\AbsenceIoClient\Endpoint\AbsenceEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\AllowanceTypeEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\DepartmentEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\LocationEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\ReasonEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\TeamEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\TimespanEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\HolidayEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\UserEndpoint;

/**
 * Main absence.io API client.
 */
class AbsenceClient
{
    private HttpClient $http;

    public function __construct(Config $config)
    {
        $this->http = new HttpClient($config);
    }

    /**
     * Get the absences endpoint.
     */
    public function absences(): AbsenceEndpoint
    {
        return new AbsenceEndpoint($this->http);
    }

    /**
     * Get the allowance types endpoint.
     */
    public function allowanceTypes(): AllowanceTypeEndpoint
    {
        return new AllowanceTypeEndpoint($this->http);
    }

    /**
     * Get the departments endpoint.
     */
    public function departments(): DepartmentEndpoint
    {
        return new DepartmentEndpoint($this->http);
    }

    /**
     * Get the locations endpoint.
     */
    public function locations(): LocationEndpoint
    {
        return new LocationEndpoint($this->http);
    }

    /**
     * Get the reasons endpoint.
     */
    public function reasons(): ReasonEndpoint
    {
        return new ReasonEndpoint($this->http);
    }

    /**
     * Get the teams endpoint.
     */
    public function teams(): TeamEndpoint
    {
        return new TeamEndpoint($this->http);
    }

    /**
     * Get the timespans endpoint.
     */
    public function timespans(): TimespanEndpoint
    {
        return new TimespanEndpoint($this->http);
    }

    /**
     * Get the holidays endpoint.
     */
    public function holidays(): HolidayEndpoint
    {
        return new HolidayEndpoint($this->http);
    }

    /**
     * Get the users endpoint.
     */
    public function users(): UserEndpoint
    {
        return new UserEndpoint($this->http);
    }
}