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

        $orders = $client->getMarketOrders([
            "order" => "-price",
            "type" => "sell",
            "dstCurrency" => "usdt"
        ]);

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

        $trades = $client->getMarketTrades([
            "srcCurrency" => "btc",
            "dstCurrency" => "rls"
        ]);

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

        $stats = $client->getMarketStats([
            "srcCurrency" => "btc",
            "dstCurrency" => "rls"
        ]);

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

    /**
     *
     * @throws Exception
     */
    public function testGetUserReferralCode()
    {
        $accessToken = getenv('NOBITEX_ACCESS_TOKEN') ?: '';

        $client = Client::create(new Config(new Bearer($accessToken)));

        $referralCode = $client->getUserReferralCode();

        $this->assertIsString($referralCode);
        $this->assertNotEmpty($referralCode);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $dotenv = Dotenv::create(__DIR__ . '/..');
        $dotenv->load();
    }
}
