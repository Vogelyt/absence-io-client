<?php

namespace Vogelyt\AbsenceIoClient\Tests\Auth;

use PHPUnit\Framework\TestCase;
use Vogelyt\AbsenceIoClient\Auth\HawkAuth;
use Dflydev\Hawk\Client\Client;
use Dflydev\Hawk\Client\ClientBuilder;
use Dflydev\Hawk\Credentials\Credentials;
use Dflydev\Hawk\Client\Request;
use Dflydev\Hawk\Header\Header;

class HawkAuthTest extends TestCase
{
    private HawkAuth $hawkAuth;
    private Client $clientMock;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(Client::class);
        $this->hawkAuth = new HawkAuth($this->clientMock);
    }

    public function testSign()
    {
        $credentials = new Credentials('test_key', 'sha256', 'test_id');

        $requestMock = $this->createMock(Request::class);
        $headerMock = $this->createMock(Header::class);
        $headerMock->method('fieldName')->willReturn('Authorization');
        $headerMock->method('fieldValue')->willReturn('Hawk test_value');
        $requestMock->method('header')->willReturn($headerMock);

        $this->clientMock->expects($this->once())
            ->method('createRequest')
            ->with($credentials, 'http://example.com', 'GET', [
                'payload' => null,
                'content_type' => null
            ])
            ->willReturn($requestMock);

        $result = $this->hawkAuth->sign('GET', 'http://example.com', 'test_id', 'test_key');

        $this->assertEquals(['Authorization' => 'Hawk test_value'], $result);
    }
}