<?php

namespace Vogelyt\AbsenceIoClient\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Vogelyt\AbsenceIoClient\Config\Config;
use Vogelyt\AbsenceIoClient\Auth\HawkAuth;
use Vogelyt\AbsenceIoClient\Auth\OAuthClient;
use Vogelyt\AbsenceIoClient\Exception\ApiException;
use Vogelyt\AbsenceIoClient\Exception\AuthException;
use Vogelyt\AbsenceIoClient\Exception\NotFoundException;
use Vogelyt\AbsenceIoClient\Exception\ValidationException;

class HttpClient
{
    private Client $client;
    private ?HawkAuth $hawkAuth;
    private ?OAuthClient $oauthClient;
    private bool $useOAuth = false;

    public function __construct(
        private Config $config,
        ?Client $client = null,
        ?HawkAuth $hawkAuth = null,
        ?OAuthClient $oauthClient = null
    ) {
        $this->hawkAuth = $hawkAuth;
        $this->oauthClient = $oauthClient;

        // Determine authentication method
        if ($this->oauthClient || $this->config->getOAuthClientId()) {
            $this->useOAuth = true;
            if (!$this->oauthClient) {
                $this->oauthClient = new OAuthClient(
                    $this->config->getOAuthClientId(),
                    $this->config->getOAuthClientSecret(),
                    $this->config->getBaseUrl()
                );
            }
        } else {
            $this->hawkAuth = $hawkAuth ?? new HawkAuth();
        }

        $this->client = $client ?? new Client([
            'base_uri' => rtrim($config->getBaseUrl(), '/') . '/',
        ]);
    }

    public function get(string $path, array $query = []): array
    {
        return $this->sendRequest('GET', $path, ['query' => $query]);
    }

    public function post(string $path, array $payload = []): array
    {
        return $this->sendRequest('POST', $path, ['json' => $payload]);
    }

    public function put(string $path, array $payload = []): array
    {
        return $this->sendRequest('PUT', $path, ['json' => $payload]);
    }

    public function delete(string $path, array $payload = []): array
    {
        return $this->sendRequest('DELETE', $path, ['json' => $payload]);
    }

    private function sendRequest(string $method, string $path, array $options = []): array
    {
        $cleanPath = ltrim($path, '/');
        $baseUrl = rtrim($this->config->getBaseUrl(), '/') . '/';
        $fullUrl = $baseUrl . $cleanPath;

        if (!empty($options['query'])) {
            $fullUrl .= '?' . http_build_query($options['query']);
        }

        // Add authentication headers
        if ($this->useOAuth && $this->oauthClient) {
            $token = $this->oauthClient->getAccessToken();
            $options['headers'] = ['Authorization' => 'Bearer ' . $token] + ($options['headers'] ?? []);
        } elseif ($this->hawkAuth) {
            $authHeader = $this->hawkAuth->sign(
                $method,
                $fullUrl,
                $this->config->getHawkId(),
                $this->config->getHawkKey()
            );
            $options['headers'] = $authHeader + ($options['headers'] ?? []);
        }

        try {
            $response = $this->client->request($method, $cleanPath, $options);
        } catch (RequestException $requestException) {
            if ($requestException->hasResponse()) {
                $response = $requestException->getResponse();
            } else {
                throw new ApiException($requestException->getMessage(), $requestException->getCode(), $requestException);
            }
        }

        $status = $response->getStatusCode();
        $bodyContent = (string) $response->getBody();

        if ($status === 401) {
            throw new AuthException('Unauthorized: ' . $bodyContent, 401);
        }
        if ($status === 422) {
            throw new ValidationException('Validation error: ' . $bodyContent, 422);
        }
        if ($status === 404) {
            throw new NotFoundException('Not found: ' . $bodyContent, 404);
        }
        if ($status >= 400) {
            throw new ApiException('API error (' . $status . '): ' . $bodyContent, $status);
        }

        return json_decode($bodyContent, true);
    }
}
