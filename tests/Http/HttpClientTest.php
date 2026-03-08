<?php

namespace Vogelyt\AbsenceIoClient\Tests\Http;

use PHPUnit\Framework\TestCase;
use Vogelyt\AbsenceIoClient\Http\HttpClient;
use Vogelyt\AbsenceIoClient\Config\Config;
use Vogelyt\AbsenceIoClient\Auth\HawkAuth;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class HttpClientTest extends TestCase
{
    private Config $config;
    private HawkAuth $hawkAuthMock;
    private Client $guzzleMock;

    protected function setUp(): void
    {
        $this->config = new Config('test_hawk_id', 'test_hawk_key');
        $this->hawkAuthMock = $this->createMock(HawkAuth::class);
        $this->guzzleMock = $this->createMock(Client::class);
    }

    public function testGet()
    {
        $this->hawkAuthMock->expects($this->once())
            ->method('sign')
            ->with('GET', 'https://app.absence.io/api/v2/test', 'test_hawk_id', 'test_hawk_key')
            ->willReturn(['Authorization' => 'Hawk test_header']);

        $responseMock = $this->createMock(ResponseInterface::class);
        $bodyMock = $this->createMock(StreamInterface::class);
        $bodyMock->method('__toString')->willReturn('{"data": "test"}');
        $responseMock->method('getBody')->willReturn($bodyMock);

        $this->guzzleMock->expects($this->once())
            ->method('request')
            ->with('GET', 'test', [
                'headers' => ['Authorization' => 'Hawk test_header'],
                'query' => []
            ])
            ->willReturn($responseMock);

        $httpClient = new HttpClient($this->config, $this->guzzleMock, $this->hawkAuthMock);

        $result = $httpClient->get('test');

        $this->assertEquals(['data' => 'test'], $result);
    }

    public function testPost()
    {
        $this->hawkAuthMock->expects($this->once())
            ->method('sign')
            ->with('POST', 'https://app.absence.io/api/v2/test', 'test_hawk_id', 'test_hawk_key')
            ->willReturn(['Authorization' => 'Hawk test_header']);

        $responseMock = $this->createMock(ResponseInterface::class);
        $bodyMock = $this->createMock(StreamInterface::class);
        $bodyMock->method('__toString')->willReturn('{"result": "ok"}');
        $responseMock->method('getBody')->willReturn($bodyMock);

        $this->guzzleMock->expects($this->once())
            ->method('request')
            ->with('POST', 'test', [
                'headers' => ['Authorization' => 'Hawk test_header'],
                'json' => ['key' => 'value']
            ])
            ->willReturn($responseMock);

        $httpClient = new HttpClient($this->config, $this->guzzleMock, $this->hawkAuthMock);

        $result = $httpClient->post('test', ['key' => 'value']);

        $this->assertEquals(['result' => 'ok'], $result);
    }
}