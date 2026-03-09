<?php

namespace Vogelyt\AbsenceIoClient\Tests\Config;

use PHPUnit\Framework\TestCase;
use Vogelyt\AbsenceIoClient\Config\Config;

class ConfigTest extends TestCase
{
    public function testHawkConfiguration()
    {
        $config = new Config('hawk-id', 'hawk-key');

        $this->assertEquals('hawk-id', $config->getHawkId());
        $this->assertEquals('hawk-key', $config->getHawkKey());
        $this->assertTrue($config->isHawkConfigured());
        $this->assertFalse($config->isOAuthConfigured());
    }

    public function testOAuthConfiguration()
    {
        $config = Config::withOAuth('oauth-client-id', 'oauth-client-secret');

        $this->assertEquals('oauth-client-id', $config->getOAuthClientId());
        $this->assertEquals('oauth-client-secret', $config->getOAuthClientSecret());
        $this->assertTrue($config->isOAuthConfigured());
        $this->assertFalse($config->isHawkConfigured());
    }

    public function testBaseUrl()
    {
        $config = new Config('id', 'key');

        $this->assertEquals('https://app.absence.io/api/v2', $config->getBaseUrl());
    }

    public function testCustomBaseUrl()
    {
        $config = new Config('id', 'key');
        $config->setBaseUrl('https://custom.example.com/api');

        $this->assertEquals('https://custom.example.com/api', $config->getBaseUrl());
    }

    public function testFluentInterface()
    {
        $config = (new Config('id', 'key'))
            ->setBaseUrl('https://custom.example.com/api');

        $this->assertEquals('https://custom.example.com/api', $config->getBaseUrl());
    }
}
