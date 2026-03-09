<?php

namespace Vogelyt\AbsenceIoClient\Config;

/**
 * Configuration for absence.io API client.
 * Supports both Hawk and OAuth 2.0 authentication.
 */
class Config
{
    private string $baseUrl = 'https://app.absence.io/api/v2';
    private ?string $oauthClientId = null;
    private ?string $oauthClientSecret = null;

    /**
     * Create a configuration with Hawk authentication.
     *
     * @param string $hawkId Hawk ID
     * @param string $hawkKey Hawk Key
     */
    public function __construct(
        private string $hawkId,
        private string $hawkKey,
    ) {}

    /**
     * Create a configuration with OAuth 2.0 authentication.
     *
     * @param string $clientId OAuth client ID
     * @param string $clientSecret OAuth client secret
     * @return self
     */
    public static function withOAuth(string $clientId, string $clientSecret): self
    {
        $config = new self('', ''); // Hawk fields are empty for OAuth
        $config->oauthClientId = $clientId;
        $config->oauthClientSecret = $clientSecret;
        return $config;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function getHawkId(): string
    {
        return $this->hawkId;
    }

    public function getHawkKey(): string
    {
        return $this->hawkKey;
    }

    /**
     * Get OAuth client ID (if configured).
     *
     * @return string|null
     */
    public function getOAuthClientId(): ?string
    {
        return $this->oauthClientId;
    }

    /**
     * Get OAuth client secret (if configured).
     *
     * @return string|null
     */
    public function getOAuthClientSecret(): ?string
    {
        return $this->oauthClientSecret;
    }

    /**
     * Check if OAuth is configured.
     *
     * @return bool
     */
    public function isOAuthConfigured(): bool
    {
        return $this->oauthClientId !== null && $this->oauthClientSecret !== null;
    }

    /**
     * Check if Hawk is configured.
     *
     * @return bool
     */
    public function isHawkConfigured(): bool
    {
        return !empty($this->hawkId) && !empty($this->hawkKey);
    }
}
