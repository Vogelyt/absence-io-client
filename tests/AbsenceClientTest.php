<?php

namespace Vogelyt\AbsenceIoClient\Tests;

use PHPUnit\Framework\TestCase;
use Vogelyt\AbsenceIoClient\AbsenceClient;
use Vogelyt\AbsenceIoClient\Config\Config;
use Vogelyt\AbsenceIoClient\Endpoint\AbsenceEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\AllowanceTypeEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\DepartmentEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\LocationEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\ReasonEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\TeamEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\TimespanEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\HolidayEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\UserEndpoint;

class AbsenceClientTest extends TestCase
{
    private AbsenceClient $client;

    protected function setUp(): void
    {
        $config = new Config('test_id', 'test_key');
        $this->client = new AbsenceClient($config);
    }

    public function testUsersReturnsUserEndpoint()
    {
        $userEndpoint = $this->client->users();
        $this->assertInstanceOf(UserEndpoint::class, $userEndpoint);
    }

    public function testAbsencesReturnsAbsenceEndpoint()
    {
        $endpoint = $this->client->absences();
        $this->assertInstanceOf(AbsenceEndpoint::class, $endpoint);
    }

    public function testAllowanceTypesReturnsAllowanceTypeEndpoint()
    {
        $endpoint = $this->client->allowanceTypes();
        $this->assertInstanceOf(AllowanceTypeEndpoint::class, $endpoint);
    }

    public function testDepartmentsReturnsDepartmentEndpoint()
    {
        $endpoint = $this->client->departments();
        $this->assertInstanceOf(DepartmentEndpoint::class, $endpoint);
    }

    public function testLocationsReturnsLocationEndpoint()
    {
        $endpoint = $this->client->locations();
        $this->assertInstanceOf(LocationEndpoint::class, $endpoint);
    }

    public function testReasonsReturnsReasonEndpoint()
    {
        $endpoint = $this->client->reasons();
        $this->assertInstanceOf(ReasonEndpoint::class, $endpoint);
    }

    public function testTeamsReturnsTeamEndpoint()
    {
        $endpoint = $this->client->teams();
        $this->assertInstanceOf(TeamEndpoint::class, $endpoint);
    }

    public function testTimespansReturnsTimespanEndpoint()
    {
        $endpoint = $this->client->timespans();
        $this->assertInstanceOf(TimespanEndpoint::class, $endpoint);
    }

    public function testHolidaysReturnsHolidayEndpoint()
    {
        $endpoint = $this->client->holidays();
        $this->assertInstanceOf(HolidayEndpoint::class, $endpoint);
    }
}