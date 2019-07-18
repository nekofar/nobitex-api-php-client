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

    public function testGetMarketOrdersFailure()
    {
        $httpClient = $this->getMockBuilder(HttpMethodsClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClient->method('post')
            ->will($this->onConsecutiveCalls(
                new Response(401),
                new Response('200', [], json_encode([
                    'status' => 'failed',
                    'message' => 'Validation Failed'
                ]))
            ));

        /** @var HttpMethodsClient $httpClient */
        $client = new Client($httpClient, new JsonMapper());

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->getMarketOrders();
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->getMarketOrders();
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Validation Failed', $exception->getMessage());
            }
        );
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

    public function testGetMarketTradesFailure()
    {
        $httpClient = $this->getMockBuilder(HttpMethodsClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClient->method('post')
            ->will($this->onConsecutiveCalls(
                new Response(401),
                new Response('200', [], json_encode([
                    'status' => 'failed',
                    'message' => 'Validation Failed'
                ]))
            ));

        /** @var HttpMethodsClient $httpClient */
        $client = new Client($httpClient, new JsonMapper());

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->getMarketTrades([
                    "srcCurrency" => "btc",
                    "dstCurrency" => "rls"
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->getMarketTrades([
                    "srcCurrency" => "btc",
                    "dstCurrency" => "rls"
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($client) {
                $client->getMarketTrades([
                    "srcCurrency" => "",
                    "dstCurrency" => "rls"
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Source currency is missing.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($client) {
                $client->getMarketTrades([
                    "srcCurrency" => "btc",
                    "dstCurrency" => ""
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Destination currency is missing.', $exception->getMessage());
            }
        );
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

    public function testGetMarketStatsFailure()
    {
        $httpClient = $this->getMockBuilder(HttpMethodsClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClient->method('post')
            ->will($this->onConsecutiveCalls(
                new Response(401),
                new Response('200', [], json_encode([
                    'status' => 'failed',
                    'message' => 'Validation Failed'
                ]))
            ));

        /** @var HttpMethodsClient $httpClient */
        $client = new Client($httpClient, new JsonMapper());

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->getMarketStats([
                    "srcCurrency" => "btc",
                    "dstCurrency" => "rls"
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->getMarketStats([
                    "srcCurrency" => "btc",
                    "dstCurrency" => "rls"
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($client) {
                $client->getMarketStats([
                    "srcCurrency" => "",
                    "dstCurrency" => "rls"
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Source currency is missing.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($client) {
                $client->getMarketStats([
                    "srcCurrency" => "btc",
                    "dstCurrency" => ""
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Destination currency is missing.', $exception->getMessage());
            }
        );
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

    public function testGetUserProfileFailure()
    {
        $httpClient = $this->getMockBuilder(HttpMethodsClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClient->method('post')
            ->will($this->onConsecutiveCalls(
                new Response(401),
                new Response('200', [], json_encode([
                    'status' => 'failed',
                    'message' => 'Validation Failed'
                ]))
            ));

        /** @var HttpMethodsClient $httpClient */
        $client = new Client($httpClient, new JsonMapper());

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->getUserProfile();
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->getUserProfile();
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Validation Failed', $exception->getMessage());
            }
        );
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
     */
    public function testGetUserLoginAttemptsFailure()
    {
        $httpClient = $this->getMockBuilder(HttpMethodsClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClient->method('post')
            ->will($this->onConsecutiveCalls(
                new Response(401),
                new Response('200', [], json_encode([
                    'status' => 'failed',
                    'message' => 'Validation Failed'
                ]))
            ));

        /** @var HttpMethodsClient $httpClient */
        $client = new Client($httpClient, new JsonMapper());

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->getUserLoginAttempts();
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->getUserLoginAttempts();
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Validation Failed', $exception->getMessage());
            }
        );
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
     */
    public function testGetUserReferralCodeFailure()
    {
        $httpClient = $this->getMockBuilder(HttpMethodsClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClient->method('post')
            ->will($this->onConsecutiveCalls(
                new Response(401),
                new Response('200', [], json_encode([
                    'status' => 'failed',
                    'message' => 'Validation Failed'
                ]))
            ));

        /** @var HttpMethodsClient $httpClient */
        $client = new Client($httpClient, new JsonMapper());

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->getUserReferralCode();
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->getUserReferralCode();
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Validation Failed', $exception->getMessage());
            }
        );
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

        $httpClient->method('post')
            ->will($this->onConsecutiveCalls(
                new Response(200),
                new Response(401),
                new Response('200', [], json_encode([
                    'status' => 'failed',
                    'message' => 'Validation Failed'
                ]))
            ));

        /** @var HttpMethodsClient $httpClient */
        $client = new Client($httpClient, new JsonMapper());

        $this->assertFalse($client->addUserCard([
            "number" => "5041721011111111",
            "bank" => "Resalat",
        ]));

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->addUserCard([
                    "number" => "5041721011111111",
                    "bank" => "Resalat",
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->addUserCard([
                    "number" => "5041721011111111",
                    "bank" => "Resalat",
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($client) {
                $client->addUserCard([
                    "number" => "",
                    "bank" => "5041721011111111",
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Card number is missing.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($client) {
                $client->addUserCard([
                    "number" => "50417210111111111",
                    "bank" => "",
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Bank name is missing.', $exception->getMessage());
            }
        );
    }


    /**
     *
     * @throws \Http\Client\Exception
     */
    public function testAddUserAccount()
    {
        $httpClient = $this->getMockBuilder(HttpMethodsClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClient->method('post')
            ->willReturn(new Response('200', [], json_encode(['status' => 'ok'])));

        /** @var HttpMethodsClient $httpClient */
        $client = new Client($httpClient, new JsonMapper());

        $status = $client->addUserAccount([
            "number" => "5041721011111111",
            "bank" => "Resalat",
            "shaba" => "IR111111111111111111111111",
        ]);

        $this->assertTrue($status);
    }

    /**
     *
     * @throws \Http\Client\Exception
     */
    public function testAddUserAccountFailure()
    {
        $httpClient = $this->getMockBuilder(HttpMethodsClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClient->method('post')
            ->will($this->onConsecutiveCalls(
                new Response(200),
                new Response(401),
                new Response('200', [], json_encode([
                    'status' => 'failed',
                    'message' => 'Validation Failed'
                ]))
            ));

        /** @var HttpMethodsClient $httpClient */
        $client = new Client($httpClient, new JsonMapper());

        $this->assertFalse($client->addUserAccount([
            "number" => "5041721011111111",
            "bank" => "Resalat",
            "shaba" => "IR111111111111111111111111",
        ]));

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->addUserAccount([
                    "number" => "5041721011111111",
                    "bank" => "Resalat",
                    "shaba" => "IR111111111111111111111111",
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($httpClient, $client) {
                $client->addUserAccount([
                    "number" => "5041721011111111",
                    "bank" => "Resalat",
                    "shaba" => "IR111111111111111111111111",
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Validation Failed', $exception->getMessage());
            }
        );
        $this->assertThrows(
            Exception::class,
            function () use ($client) {
                $client->addUserAccount([
                    "number" => "",
                    "bank" => "5041721011111111",
                    "shaba" => "IR111111111111111111111111",
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Account number is missing.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($client) {
                $client->addUserAccount([
                    "number" => "50417210111111111",
                    "bank" => "",
                    "shaba" => "IR111111111111111111111111",
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Bank name is missing.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            Exception::class,
            function () use ($client) {
                $client->addUserAccount([
                    "number" => "50417210111111111",
                    "bank" => "Resalat",
                    "shaba" => "IR1111111111111111111111110",
                ]);
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Account shaba is missing.', $exception->getMessage());
            }
        );
    }


    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        try {
            $dotenv = Dotenv::create(__DIR__ . '/..');
            $dotenv->load();
        } catch (Exception $e) {
        }

        self::$username = getenv('NOBITEX_USERNAME') ?: 'username';
        self::$password = getenv('NOBITEX_PASSWORD') ?: 'password';

        self::$accessToken = getenv('NOBITEX_ACCESS_TOKEN') ?: '';
    }

}
