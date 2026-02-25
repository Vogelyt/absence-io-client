<?php

namespace Vogelyt\AbsenceIoClient\Config;

class Config
{
    private string $baseUrl = 'https://app.absence.io/api/v2';
    public function __construct(
        private string $hawkId,
        private string $hawkKey,
    ) {}

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getHawkId(): string
    {
        return $this->hawkId;
    }

    public function getHawkKey(): string
    {
        return $this->hawkKey;
    }
}