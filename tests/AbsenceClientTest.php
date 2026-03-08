<?php

namespace Vogelyt\AbsenceIoClient\Tests;

use PHPUnit\Framework\TestCase;
use Vogelyt\AbsenceIoClient\AbsenceClient;
use Vogelyt\AbsenceIoClient\Config\Config;
use Vogelyt\AbsenceIoClient\Endpoint\UserEndpoint;

class AbsenceClientTest extends TestCase
{
    public function testUsersReturnsUserEndpoint()
    {
        $config = new Config('test_id', 'test_key');
        $client = new AbsenceClient($config);

        $userEndpoint = $client->users();

        $this->assertInstanceOf(UserEndpoint::class, $userEndpoint);
    }
}