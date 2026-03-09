<?php

namespace Vogelyt\AbsenceIoClient\Tests\Endpoint;

use PHPUnit\Framework\TestCase;
use Vogelyt\AbsenceIoClient\Endpoint\UserEndpoint;
use Vogelyt\AbsenceIoClient\Http\HttpClient;
use Vogelyt\AbsenceIoClient\Query\QueryBuilder;

class UserEndpointTest extends TestCase
{
    private HttpClient $httpClientMock;
    private UserEndpoint $userEndpoint;

    protected function setUp(): void
    {
        $this->httpClientMock = $this->createMock(HttpClient::class);
        $this->userEndpoint = new UserEndpoint($this->httpClientMock);
    }

    public function testGetAllWithoutQuery()
    {
        $this->httpClientMock->expects($this->once())
            ->method('post')
            ->with('users', [])
            ->willReturn([['id' => 1, 'name' => 'John']]);

        $result = $this->userEndpoint->getAll();

        $this->assertCount(1, $result);
        $this->assertEquals('John', $result[0]['name']);
    }

    public function testGetAllWithQuery()
    {
        $query = (new QueryBuilder())
            ->where('status', 'active')
            ->limit(10);

        $expectedPayload = $query->build();

        $this->httpClientMock->expects($this->once())
            ->method('post')
            ->with('users', $expectedPayload)
            ->willReturn([['id' => 1, 'name' => 'John']]);

        $result = $this->userEndpoint->getAll($query);

        $this->assertCount(1, $result);
    }

    public function testGetSingle()
    {
        $this->httpClientMock->expects($this->once())
            ->method('get')
            ->with('users/123')
            ->willReturn(['id' => 123, 'name' => 'John Doe']);

        $result = $this->userEndpoint->getSingle(123);

        $this->assertEquals('John Doe', $result['name']);
    }

    public function testCreateThrowsException()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Users cannot be created directly; use invite() instead.');

        // attempt to call the inherited create method; should be blocked
        $this->userEndpoint->create(['name' => 'Jane', 'email' => 'jane@example.com']);
    }

    public function testUpdate()
    {
        $updateData = ['name' => 'John Updated'];

        $this->httpClientMock->expects($this->once())
            ->method('put')
            ->with('users/123', $updateData)
            ->willReturn(['id' => 123, 'name' => 'John Updated']);

        $result = $this->userEndpoint->update(123, $updateData);

        $this->assertEquals('John Updated', $result['name']);
    }

    public function testDelete()
    {
        $this->httpClientMock->expects($this->once())
            ->method('delete')
            ->with('users/123')
            ->willReturn([]);

        $result = $this->userEndpoint->delete(123);

        $this->assertIsArray($result);
    }

    public function testInvite()
    {
        $payload = [
            'email' => 'newuser@example.com',
            'firstName' => 'Foo',
            'lastName' => 'Bar',
            'roleId' => '000000000000000000001000',
        ];

        $this->httpClientMock->expects($this->once())
            ->method('post')
            ->with('users/invite', $payload)
            ->willReturn(['status' => 'invited', 'email' => 'newuser@example.com']);

        $result = $this->userEndpoint->invite('newuser@example.com', [
            'firstName' => 'Foo',
            'lastName' => 'Bar',
            'roleId' => '000000000000000000001000',
        ]);

        $this->assertEquals('invited', $result['status']);
    }

    public function testQueryBuilder()
    {
        $query = $this->userEndpoint->query();

        $this->assertInstanceOf(QueryBuilder::class, $query);
    }
}
