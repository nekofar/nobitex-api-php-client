<?php

namespace Nekofar\Nobitex;

use Nekofar\Nobitex\Auth\Basic;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testCreateHttpClient()
    {
        $username = 'username';
        $password = 'password';

        $config = new Config(new Basic($username, $password));
        $this->assertInstanceOf('Http\Client\Common\HttpMethodsClient', $config->createHttpClient());
    }

    public function testCreateJsonMapper()
    {
        $username = 'username';
        $password = 'password';

        $config = new Config(new Basic($username, $password));
        $this->assertInstanceOf('JsonMapper', $config->createJsonMapper());
    }
}
