<?php

namespace Vogelyt\AbsenceIoClient\Http;

use GuzzleHttp\Client;
use Vogelyt\AbsenceIoClient\Config\Config;
use Vogelyt\AbsenceIoClient\Auth\HawkAuth;

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
        $cleanPath = ltrim($path, '/');
        $baseUrl = rtrim($this->config->getBaseUrl(), '/') . '/';
        $fullUrl = $baseUrl . $cleanPath;

        if (!empty($query)) {
            $fullUrl .= '?' . http_build_query($query);
        }

        $authHeader = $this->hawkAuth->sign(
            'GET',
            $fullUrl,
            $this->config->getHawkId(),
            $this->config->getHawkKey()
        );

        $response = $this->client->request('GET', $cleanPath, [
            'headers' => $authHeader,
            'query'   => $query,
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public function post(string $path, array $payload = []): array
    {
        $cleanPath = ltrim($path, '/');
        $baseUrl = rtrim($this->config->getBaseUrl(), '/') . '/';
        $fullUrl = $baseUrl . $cleanPath;

        $authHeader = $this->hawkAuth->sign(
            'POST',
            $fullUrl,
            $this->config->getHawkId(),
            $this->config->getHawkKey()
        );

        $response = $this->client->request('POST', $cleanPath, [
            'headers' => $authHeader,
            'json'    => $payload,
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public function put(string $path, array $payload = []): array
    {
        $cleanPath = ltrim($path, '/');
        $baseUrl = rtrim($this->config->getBaseUrl(), '/') . '/';
        $fullUrl = $baseUrl . $cleanPath;

        $authHeader = $this->hawkAuth->sign(
            'PUT',
            $fullUrl,
            $this->config->getHawkId(),
            $this->config->getHawkKey()
        );

        $response = $this->client->request('PUT', $cleanPath, [
            'headers' => $authHeader,
            'json'    => $payload,
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public function delete(string $path, array $payload = []): array
    {
        $cleanPath = ltrim($path, '/');
        $baseUrl = rtrim($this->config->getBaseUrl(), '/') . '/';
        $fullUrl = $baseUrl . $cleanPath;

        $authHeader = $this->hawkAuth->sign(
            'DELETE',
            $fullUrl,
            $this->config->getHawkId(),
            $this->config->getHawkKey()
        );

        $response = $this->client->request('DELETE', $cleanPath, [
            'headers' => $authHeader,
            'json'    => $payload,
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}