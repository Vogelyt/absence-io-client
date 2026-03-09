<?php

namespace Vogelyt\AbsenceIoClient\Tests\Endpoint;

use PHPUnit\Framework\TestCase;
use Vogelyt\AbsenceIoClient\Endpoint\AbsenceEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\DepartmentEndpoint;
use Vogelyt\AbsenceIoClient\Endpoint\TeamEndpoint;
use Vogelyt\AbsenceIoClient\Http\HttpClient;
use Vogelyt\AbsenceIoClient\Query\QueryBuilder;

class AbstractEndpointTest extends TestCase
{
    private HttpClient $httpClientMock;

    protected function setUp(): void
    {
        $this->httpClientMock = $this->createMock(HttpClient::class);
    }

    public function testAbsenceEndpointGetAll()
    {
        $endpoint = new AbsenceEndpoint($this->httpClientMock);

        $this->httpClientMock->expects($this->once())
            ->method('post')
            ->with('absences', [])
            ->willReturn([['id' => 1, 'start' => '2024-01-01']]);

        $result = $endpoint->getAll();

        $this->assertCount(1, $result);
    }

    public function testAbsenceEndpointGetSingle()
    {
        $endpoint = new AbsenceEndpoint($this->httpClientMock);

        $this->httpClientMock->expects($this->once())
            ->method('get')
            ->with('absences/123')
            ->willReturn(['id' => 123, 'start' => '2024-01-01']);

        $result = $endpoint->getSingle(123);

        $this->assertEquals(123, $result['id']);
    }

    public function testAbsenceEndpointCreate()
    {
        $endpoint = new AbsenceEndpoint($this->httpClientMock);
        $data = ['start' => '2024-01-01', 'end' => '2024-01-05'];

        $this->httpClientMock->expects($this->once())
            ->method('post')
            ->with('absences/create', $data)
            ->willReturn(['id' => 999, 'start' => '2024-01-01', 'end' => '2024-01-05']);

        $result = $endpoint->create($data);

        $this->assertEquals(999, $result['id']);
    }

    public function testAbsenceEndpointUpdate()
    {
        $endpoint = new AbsenceEndpoint($this->httpClientMock);
        $data = ['status' => 'approved'];

        $this->httpClientMock->expects($this->once())
            ->method('put')
            ->with('absences/123', $data)
            ->willReturn(['id' => 123, 'status' => 'approved']);

        $result = $endpoint->update(123, $data);

        $this->assertEquals('approved', $result['status']);
    }

    public function testAbsenceEndpointDelete()
    {
        $endpoint = new AbsenceEndpoint($this->httpClientMock);

        $this->httpClientMock->expects($this->once())
            ->method('delete')
            ->with('absences/123')
            ->willReturn([]);

        $endpoint->delete(123);

        $this->assertTrue(true); // Just ensure no exception
    }

    public function testDepartmentEndpointCrud()
    {
        $endpoint = new DepartmentEndpoint($this->httpClientMock);

        $this->httpClientMock->expects($this->once())
            ->method('get')
            ->with('departments/42')
            ->willReturn(['id' => 42, 'name' => 'Engineering']);

        $result = $endpoint->getSingle(42);

        $this->assertEquals('Engineering', $result['name']);
    }

    public function testTeamEndpointCrud()
    {
        $endpoint = new TeamEndpoint($this->httpClientMock);

        $this->httpClientMock->expects($this->once())
            ->method('get')
            ->with('teams/7')
            ->willReturn(['id' => 7, 'name' => 'Backend']);

        $result = $endpoint->getSingle(7);

        $this->assertEquals('Backend', $result['name']);
    }

    public function testQueryBuilderIntegration()
    {
        $endpoint = new AbsenceEndpoint($this->httpClientMock);
        $query = $endpoint->query();

        $this->assertInstanceOf(QueryBuilder::class, $query);
    }

    public function testEndpointWithComplexQuery()
    {
        $endpoint = new AbsenceEndpoint($this->httpClientMock);

        $query = $endpoint->query()
            ->where('status', 'pending')
            ->whereGreaterThan('created_at', '2024-01-01')
            ->orderByDesc('created_at')
            ->limit(25);

        $expectedPayload = $query->build();

        $this->httpClientMock->expects($this->once())
            ->method('post')
            ->with('absences', $expectedPayload)
            ->willReturn([]);

        $endpoint->getAll($query);

        $this->assertTrue(true);
    }
}
