<?php

namespace Vogelyt\AbsenceIoClient\Tests\Endpoint;

use PHPUnit\Framework\TestCase;
use Vogelyt\AbsenceIoClient\Endpoint\UserEndpoint;
use Vogelyt\AbsenceIoClient\Http\HttpClient;

class UserEndpointTest extends TestCase
{
    public function testGetAllCallsHttpPost()
    {
        $httpClientMock = $this->createMock(HttpClient::class);
        $httpClientMock->expects($this->once())
            ->method('post')
            ->with('users', [])
            ->willReturn(['user1', 'user2']);

        $userEndpoint = new UserEndpoint($httpClientMock);

        $result = $userEndpoint->getAll();

        $this->assertEquals(['user1', 'user2'], $result);
    }
}