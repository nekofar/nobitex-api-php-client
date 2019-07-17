<?php
/**
 * @package Nekofar\Nobitex
 * @author Milad Nekofar <milad@nekofar.com>
 */

namespace Nekofar\Nobitex;

use Dotenv\Dotenv;
use Exception;
use GuzzleHttp\Psr7\Response;
use Http\Client\Common\HttpMethodsClient;
use Jchook\AssertThrows\AssertThrows;
use JsonMapper;
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
    use AssertThrows;

    /**
     * @var string
     */
    private static $username;
    /**
     * @var string
     */
    private static $password;
    /**
     * @var string
     */
    private static $accessToken;

    /**
     * @throws \Http\Client\Exception
     * @throws JsonMapper_Exception
     */
    public function testGetMarketOrders()
    {
        $client = Client::create(Config::doAuth(self::$username, self::$password));

        $orders = $client->getMarketOrders([
            "order" => "-price",
            "type" => "sell",
            "dstCurrency" => "usdt"
        ]);

        $this->assertIsArray($orders);
        $this->assertContainsOnlyInstancesOf(Order::class, $orders);
    }

    /**
     * @throws \Http\Client\Exception
     * @throws JsonMapper_Exception
     */
    public function testGetMarketTrades()
    {
        $client = Client::create(Config::doAuth(self::$username, self::$password));

        $trades = $client->getMarketTrades([
            "srcCurrency" => "btc",
            "dstCurrency" => "rls"
        ]);

        $this->assertIsArray($trades);
        $this->assertContainsOnlyInstancesOf(Trade::class, $trades);
    }

    /**
     * @throws \Http\Client\Exception
     */
    public function testGetMarketStats()
    {
        $client = Client::create(Config::doAuth(self::$username, self::$password));

        $stats = $client->getMarketStats([
            "srcCurrency" => "btc",
            "dstCurrency" => "rls"
        ]);

        $this->assertIsArray($stats);
        $this->assertNotEmpty($stats);
    }

    /**
     * @throws \Http\Client\Exception
     * @throws JsonMapper_Exception
     */
    public function testGetUserProfile()
    {
        $client = Client::create(new Config(new Bearer(self::$accessToken)));

        $profile = $client->getUserProfile();

        $this->assertIsObject($profile);
        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertContainsOnlyInstancesOf(Card::class, $profile->cards);
        $this->assertContainsOnlyInstancesOf(Account::class, $profile->accounts);
    }

    /**
     * @throws \Http\Client\Exception
     */
    public function testGetUserLoginAttempts()
    {
        $client = Client::create(new Config(new Bearer(self::$accessToken)));

        $attempts = $client->getUserLoginAttempts();

        $this->assertIsArray($attempts);
        $this->assertNotEmpty($attempts);
    }

    /**
     *
     * @throws \Http\Client\Exception
     */
    public function testGetUserReferralCode()
    {
        $client = Client::create(new Config(new Bearer(self::$accessToken)));

        $referralCode = $client->getUserReferralCode();

        $this->assertIsString($referralCode);
        $this->assertNotEmpty($referralCode);
    }

    /**
     *
     * @throws \Http\Client\Exception
     */
    public function testAddUserCard()
    {
        $httpClient = $this->getMockBuilder(HttpMethodsClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClient->method('post')
            ->willReturn(new Response('200', [], json_encode(['status' => 'ok'])));

        /** @var HttpMethodsClient $httpClient */
        $client = new Client($httpClient, new JsonMapper());

        $status = $client->addUserCard([
            "number" => "5041721011111111",
            "bank" => "Resalat"
        ]);

        $this->assertTrue($status);
    }

    /**
     *
     * @throws \Http\Client\Exception
     */
    public function testAddUserCardFailure()
    {
        $httpClient = $this->getMockBuilder(HttpMethodsClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var HttpMethodsClient $httpClient */
        $client = new Client($httpClient, new JsonMapper());


        $httpClient->method('post')
            ->willReturn(new Response('200', [], json_encode(['status' => 'failure'])));

        $status = $client->addUserCard([
            "number" => "5041721011111111",
            "bank" => "Resalat"
        ]);
        $this->assertFalse($status);

        $this->assertThrows(Exception::class, function () use ($client) {
            $client->addUserCard([
                "number" => "50417210111111111",
                "bank" => "Resalat"
            ]);
        });

        $this->assertThrows(Exception::class, function () use ($client) {
            $client->addUserCard([
                "number" => "50417210111111111",
                "bank" => ""
            ]);
        });
    }


    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $dotenv = Dotenv::create(__DIR__ . '/..');
        $dotenv->load();

        self::$username = getenv('NOBITEX_USERNAME') ?: 'username';
        self::$password = getenv('NOBITEX_PASSWORD') ?: 'password';

        self::$accessToken = getenv('NOBITEX_ACCESS_TOKEN') ?: '';
    }

}
