<?php

namespace Vogelyt\AbsenceIoClient\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Vogelyt\AbsenceIoClient\Config\Config;
use Vogelyt\AbsenceIoClient\Auth\HawkAuth;
use Vogelyt\AbsenceIoClient\Exception\ApiException;
use Vogelyt\AbsenceIoClient\Exception\AuthException;
use Vogelyt\AbsenceIoClient\Exception\NotFoundException;
use Vogelyt\AbsenceIoClient\Exception\ValidationException;

class HttpClient
{
    private Client $client;
    private HawkAuth $hawkAuth;

    public function __construct(
        private Config $config,
        ?Client $client = null,
        ?HawkAuth $hawkAuth = null
    ) {
        $this->hawkAuth = $hawkAuth ?? new HawkAuth();

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

        $authHeader = $this->hawkAuth->sign(
            $method,
            $fullUrl,
            $this->config->getHawkId(),
            $this->config->getHawkKey()
        );

        $options['headers'] = $authHeader + ($options['headers'] ?? []);

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
        if ($status === 401) {
            throw new AuthException('Unauthorized', 401);
        }
        if ($status === 422) {
            throw new ValidationException('Validation error', 422);
        }
        if ($status === 404) {
            throw new NotFoundException('Not found', 404);
        }
        if ($status >= 400) {
            throw new ApiException('API error', $status);
        }

        return json_decode((string) $response->getBody(), true);
    }
}