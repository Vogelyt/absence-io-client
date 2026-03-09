<?php

namespace Vogelyt\AbsenceIoClient\Tests\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Vogelyt\AbsenceIoClient\Auth\OAuthClient;
use Vogelyt\AbsenceIoClient\Exception\AuthException;

class OAuthClientTest extends TestCase
{
    private OAuthClient $oauthClient;

    protected function setUp(): void
    {
        $this->oauthClient = new OAuthClient('test-client-id', 'test-client-secret');
    }

    public function testGetAccessTokenFetchesNewToken()
    {
        $clientMock = $this->createMock(Client::class);
        $response = new Response(200, [], json_encode([
            'access_token' => 'test-token-123',
            'expires_in' => 3600,
        ]));

        $clientMock->expects($this->once())
            ->method('post')
            ->willReturn($response);

        // Use reflection to inject the mock client
        $reflection = new \ReflectionClass($this->oauthClient);
        $property = $reflection->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($this->oauthClient, $clientMock);

        $token = $this->oauthClient->getAccessToken();

        $this->assertEquals('test-token-123', $token);
    }

    public function testGetAccessTokenReturnsCachedToken()
    {
        // Set a valid token directly
        $this->oauthClient->setToken('cached-token', 3600);

        // Should not require any HTTP calls
        $token = $this->oauthClient->getAccessToken();

        $this->assertEquals('cached-token', $token);
    }

    public function testTokenValidation()
    {
        $this->assertFalse($this->oauthClient->isTokenValid());

        $this->oauthClient->setToken('valid-token', 3600);

        $this->assertTrue($this->oauthClient->isTokenValid());
    }

    public function testClearToken()
    {
        $this->oauthClient->setToken('some-token', 3600);
        $this->assertTrue($this->oauthClient->isTokenValid());

        $this->oauthClient->clearToken();

        $this->assertFalse($this->oauthClient->isTokenValid());
    }

    public function testManualTokenSet()
    {
        $this->oauthClient->setToken('manual-token', 1800);

        $this->assertEquals('manual-token', $this->oauthClient->getAccessToken());
        $this->assertTrue($this->oauthClient->isTokenValid());
    }

    public function testTokenExpirationDetection()
    {
        // Set token that expires immediately
        $this->oauthClient->setToken('expiring-token', 1);

        sleep(2); // Wait for token to expire

        $this->assertFalse($this->oauthClient->isTokenValid());
    }
}
