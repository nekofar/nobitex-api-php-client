<?php

namespace Nekofar\Nobitex;

use Dotenv\Dotenv;
use Http\Client\Exception;
use JsonMapper_Exception;
use Nekofar\Nobitex\Model\Order;
use Nekofar\Nobitex\Model\Trade;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $dotenv = Dotenv::create(__DIR__ . '/..');
        $dotenv->load();
    }

    /**
     * @throws Exception
     * @throws JsonMapper_Exception
     */
    public function testGetMarketOrders()
    {
        $username = getenv('NOBITEX_USERNAME') ?: 'username';
        $password = getenv('NOBITEX_PASSWORD') ?: 'password';

        $client = Client::create(Config::doAuth($username, $password));

        $orders = $client->getMarketOrders();

        $this->assertIsArray($orders);
        $this->assertContainsOnlyInstancesOf(Order::class, $orders);
    }

    /**
     * @throws Exception
     * @throws JsonMapper_Exception
     */
    public function testGetMarketTrades()
    {
        $username = getenv('NOBITEX_USERNAME') ?: 'username';
        $password = getenv('NOBITEX_PASSWORD') ?: 'password';

        $client = Client::create(Config::doAuth($username, $password));

        $trades = $client->getMarketTrades();

        $this->assertIsArray($trades);
        $this->assertContainsOnlyInstancesOf(Trade::class, $trades);
    }
}
