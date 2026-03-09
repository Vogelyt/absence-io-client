<?php

namespace Vogelyt\AbsenceIoClient\Tests\Http;

use PHPUnit\Framework\TestCase;
use Vogelyt\AbsenceIoClient\Http\HttpClient;
use Vogelyt\AbsenceIoClient\Config\Config;
use Vogelyt\AbsenceIoClient\Auth\HawkAuth;
use Vogelyt\AbsenceIoClient\Auth\OAuthClient;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Vogelyt\AbsenceIoClient\Exception\AuthException;
use Vogelyt\AbsenceIoClient\Exception\ValidationException;
use Vogelyt\AbsenceIoClient\Exception\NotFoundException;
use Vogelyt\AbsenceIoClient\Exception\ApiException;

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
        $responseMock->method('getStatusCode')->willReturn(200);
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
        $responseMock->method('getStatusCode')->willReturn(200);
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

    public function testPut()
    {
        $this->hawkAuthMock->expects($this->once())
            ->method('sign')
            ->with('PUT', 'https://app.absence.io/api/v2/test', 'test_hawk_id', 'test_hawk_key')
            ->willReturn(['Authorization' => 'Hawk test_header']);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(200);
        $bodyMock = $this->createMock(StreamInterface::class);
        $bodyMock->method('__toString')->willReturn('{"result": "updated"}');
        $responseMock->method('getBody')->willReturn($bodyMock);

        $this->guzzleMock->expects($this->once())
            ->method('request')
            ->with('PUT', 'test', [
                'headers' => ['Authorization' => 'Hawk test_header'],
                'json' => ['key' => 'updated_value']
            ])
            ->willReturn($responseMock);

        $httpClient = new HttpClient($this->config, $this->guzzleMock, $this->hawkAuthMock);

        $result = $httpClient->put('test', ['key' => 'updated_value']);

        $this->assertEquals(['result' => 'updated'], $result);
    }

    public function testDelete()
    {
        $this->hawkAuthMock->expects($this->once())
            ->method('sign')
            ->with('DELETE', 'https://app.absence.io/api/v2/test', 'test_hawk_id', 'test_hawk_key')
            ->willReturn(['Authorization' => 'Hawk test_header']);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(200);
        $bodyMock = $this->createMock(StreamInterface::class);
        $bodyMock->method('__toString')->willReturn('{"result": "deleted"}');
        $responseMock->method('getBody')->willReturn($bodyMock);

        $this->guzzleMock->expects($this->once())
            ->method('request')
            ->with('DELETE', 'test', [
                'headers' => ['Authorization' => 'Hawk test_header'],
                'json' => []
            ])
            ->willReturn($responseMock);

        $httpClient = new HttpClient($this->config, $this->guzzleMock, $this->hawkAuthMock);

        $result = $httpClient->delete('test');

        $this->assertEquals(['result' => 'deleted'], $result);
    }

    private function createResponseWithStatus(int $status): ResponseInterface
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn($status);
        $bodyMock = $this->createMock(StreamInterface::class);
        $bodyMock->method('__toString')->willReturn('');
        $responseMock->method('getBody')->willReturn($bodyMock);
        return $responseMock;
    }

    public function testThrowsAuthExceptionOn401()
    {
        $this->expectException(AuthException::class);

        $this->hawkAuthMock->method('sign')
            ->willReturn(['Authorization' => 'Hawk header']);

        $this->guzzleMock->method('request')
            ->willReturn($this->createResponseWithStatus(401));

        $httpClient = new HttpClient($this->config, $this->guzzleMock, $this->hawkAuthMock);
        $httpClient->get('test');
    }

    public function testThrowsValidationExceptionOn422()
    {
        $this->expectException(ValidationException::class);

        $this->hawkAuthMock->method('sign')->willReturn(['Authorization' => 'Hawk header']);
        $this->guzzleMock->method('request')->willReturn($this->createResponseWithStatus(422));

        $httpClient = new HttpClient($this->config, $this->guzzleMock, $this->hawkAuthMock);
        $httpClient->get('test');
    }

    public function testThrowsNotFoundExceptionOn404()
    {
        $this->expectException(NotFoundException::class);

        $this->hawkAuthMock->method('sign')->willReturn(['Authorization' => 'Hawk header']);
        $this->guzzleMock->method('request')->willReturn($this->createResponseWithStatus(404));

        $httpClient = new HttpClient($this->config, $this->guzzleMock, $this->hawkAuthMock);
        $httpClient->get('test');
    }

    public function testThrowsGenericApiExceptionOnOtherError()
    {
        $this->expectException(ApiException::class);

        $this->hawkAuthMock->method('sign')->willReturn(['Authorization' => 'Hawk header']);
        $this->guzzleMock->method('request')->willReturn($this->createResponseWithStatus(500));

        $httpClient = new HttpClient($this->config, $this->guzzleMock, $this->hawkAuthMock);
        $httpClient->get('test');
    }

    public function testOAuthAuthenticationWithBearerToken()
    {
        $oauthClientMock = $this->createMock(OAuthClient::class);
        $oauthClientMock->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('oauth-token-123');

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(200);
        $bodyMock = $this->createMock(StreamInterface::class);
        $bodyMock->method('__toString')->willReturn('{"data": "test"}');
        $responseMock->method('getBody')->willReturn($bodyMock);

        $this->guzzleMock->expects($this->once())
            ->method('request')
            ->with('GET', 'test', [
                'headers' => ['Authorization' => 'Bearer oauth-token-123'],
                'query' => []
            ])
            ->willReturn($responseMock);

        $httpClient = new HttpClient($this->config, $this->guzzleMock, null, $oauthClientMock);

        $result = $httpClient->get('test');

        $this->assertEquals(['data' => 'test'], $result);
    }

    public function testOAuthConfigurationDetection()
    {
        $oauthConfig = Config::withOAuth('oauth-id', 'oauth-secret');
        $oauthClientMock = $this->createMock(OAuthClient::class);
        $oauthClientMock->method('getAccessToken')->willReturn('token');

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(200);
        $bodyMock = $this->createMock(StreamInterface::class);
        $bodyMock->method('__toString')->willReturn('{}');
        $responseMock->method('getBody')->willReturn($bodyMock);

        $this->guzzleMock->method('request')->willReturn($responseMock);

        $httpClient = new HttpClient($oauthConfig, $this->guzzleMock, null, $oauthClientMock);

        // Should use OAuth, not Hawk
        $this->assertTrue($oauthConfig->isOAuthConfigured());
        $httpClient->get('test');
    }
}