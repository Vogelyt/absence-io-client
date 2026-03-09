<?php

namespace Vogelyt\AbsenceIoClient\Auth;

use GuzzleHttp\Client;
use Vogelyt\AbsenceIoClient\Exception\ApiException;
use Vogelyt\AbsenceIoClient\Exception\AuthException;

/**
 * OAuth 2.0 Client for absence.io API.
 * Handles token fetching, refreshing, and management.
 */
class OAuthClient
{
    private ?string $accessToken = null;
    private ?int $tokenExpiresAt = null;
    private Client $httpClient;

    /**
     * @param string $clientId OAuth client ID
     * @param string $clientSecret OAuth client secret
     * @param string $baseUrl API base URL (default: https://app.absence.io/api)
     */
    public function __construct(
        private string $clientId,
        private string $clientSecret,
        private string $baseUrl = 'https://app.absence.io/api'
    ) {
        $this->httpClient = new Client();
    }

    /**
     * Get a valid access token. Fetches a new one if needed or expired.
     *
     * @return string Access token
     * @throws AuthException If token fetching fails
     */
    public function getAccessToken(): string
    {
        // Return cached token if still valid (with 5-minute buffer)
        if ($this->accessToken && $this->tokenExpiresAt && $this->tokenExpiresAt > time() + 300) {
            return $this->accessToken;
        }

        // Fetch new token
        $this->fetchToken();

        if (!$this->accessToken) {
            throw new AuthException('Failed to obtain OAuth access token', 401);
        }

        return $this->accessToken;
    }

    /**
     * Fetch a new access token from the API.
     *
     * @throws AuthException If token fetching fails
     */
    private function fetchToken(): void
    {
        $url = rtrim($this->baseUrl, '/') . '/oauth/accesstoken';

        try {
            $response = $this->httpClient->post($url, [
                'json' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            if (!isset($data['access_token'])) {
                throw new AuthException('Invalid token response from server', 401);
            }

            $this->accessToken = $data['access_token'];

            // Set expiration time (subtract 60 seconds for safety margin)
            if (isset($data['expires_in'])) {
                $this->tokenExpiresAt = time() + $data['expires_in'] - 60;
            }
        } catch (\Exception $e) {
            if ($e instanceof AuthException) {
                throw $e;
            }
            throw new AuthException('Failed to fetch OAuth token: ' . $e->getMessage(), 401, $e);
        }
    }

    /**
     * Manually refresh the access token.
     *
     * @throws AuthException If token refresh fails
     */
    public function refreshToken(): string
    {
        $this->accessToken = null;
        $this->tokenExpiresAt = null;
        return $this->getAccessToken();
    }

    /**
     * Check if the token is still valid (has not expired).
     *
     * @return bool
     */
    public function isTokenValid(): bool
    {
        return $this->accessToken && $this->tokenExpiresAt && $this->tokenExpiresAt > time();
    }

    /**
     * Clear the cached token.
     */
    public function clearToken(): void
    {
        $this->accessToken = null;
        $this->tokenExpiresAt = null;
    }

    /**
     * Set a token manually (useful for testing or external token management).
     *
     * @param string $token
     * @param int|null $expiresIn Token expiration time in seconds from now
     */
    public function setToken(string $token, ?int $expiresIn = null): void
    {
        $this->accessToken = $token;
        if ($expiresIn) {
            $this->tokenExpiresAt = time() + $expiresIn - 60;
        }
    }
}
