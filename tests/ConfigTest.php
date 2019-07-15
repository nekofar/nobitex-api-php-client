<?php

namespace Nekofar\Nobitex;

use Http\Client\Common\HttpMethodsClient;
use JsonMapper;
use Nekofar\Nobitex\Auth\Basic;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testCreateHttpClient()
    {
        $username = 'username';
        $password = 'password';

        $config = new Config(new Basic($username, $password));
        $this->assertInstanceOf(HttpMethodsClient::class, $config->createHttpClient());
    }

    public function testCreateJsonMapper()
    {
        $username = 'username';
        $password = 'password';

        $config = new Config(new Basic($username, $password));
        $this->assertInstanceOf(JsonMapper::class, $config->createJsonMapper());
    }
}
