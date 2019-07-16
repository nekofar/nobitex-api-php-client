<?php
/**
 * @package Nekofar\Nobitex
 * @author Milad Nekofar <milad@nekofar.com>
 */

namespace Nekofar\Nobitex;

use Dotenv\Dotenv;
use Http\Client\Exception;
use JsonMapper_Exception;
use Nekofar\Nobitex\Auth\Bearer;
use Nekofar\Nobitex\Model\Account;
use Nekofar\Nobitex\Model\Card;
use Nekofar\Nobitex\Model\Order;
use Nekofar\Nobitex\Model\Profile;
use Nekofar\Nobitex\Model\Trade;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
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

    /**
     * @throws Exception
     */
    public function testGetMarketStats()
    {
        $username = getenv('NOBITEX_USERNAME') ?: 'username';
        $password = getenv('NOBITEX_PASSWORD') ?: 'password';

        $client = Client::create(Config::doAuth($username, $password));

        $stats = $client->getMarketStats();

        $this->assertIsArray($stats);
        $this->assertNotEmpty($stats);
    }

    /**
     * @throws Exception
     * @throws JsonMapper_Exception
     */
    public function testGetUserProfile()
    {
        $accessToken = getenv('NOBITEX_ACCESS_TOKEN') ?: '';

        $client = Client::create(new Config(new Bearer($accessToken)));

        $profile = $client->getUserProfile();

        $this->assertIsObject($profile);
        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertContainsOnlyInstancesOf(Card::class, $profile->cards);
        $this->assertContainsOnlyInstancesOf(Account::class, $profile->accounts);
    }

    /**
     * @throws Exception
     */
    public function testGetUserLoginAttempts()
    {
        $accessToken = getenv('NOBITEX_ACCESS_TOKEN') ?: '';

        $client = Client::create(new Config(new Bearer($accessToken)));

        $attempts = $client->getUserLoginAttempts();

        $this->assertIsArray($attempts);
        $this->assertNotEmpty($attempts);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $dotenv = Dotenv::create(__DIR__ . '/..');
        $dotenv->load();
    }
}
