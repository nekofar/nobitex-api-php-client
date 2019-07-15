<?php

namespace Nekofar\Nobitex;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $dotenv = Dotenv::create(__DIR__ . '/..');
        $dotenv->load();
    }


    public function testGetMarketOrders()
    {
        $username = getenv('NOBITEX_USERNAME') ?: 'username';
        $password = getenv('NOBITEX_PASSWORD') ?: 'password';

        $client = Client::create(Config::doAuth($username, $password));

        $orders = $client->getMarketOrders();

        $this->assertIsArray($orders);
    }
}
