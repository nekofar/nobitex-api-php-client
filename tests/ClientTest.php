<?php
/**
 * @package Nekofar\Nobitex
 * @author Milad Nekofar <milad@nekofar.com>
 */

namespace Nekofar\Nobitex;

use Exception;
use GuzzleHttp\Psr7\Response;
use Http\Client\Common\Exception\ClientErrorException;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\MessageFactoryDiscovery;
use Jchook\AssertThrows\AssertThrows;
use JsonMapper;
use JsonMapper_Exception;
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
     * @var \Http\Mock\Client
     */
    private static $mockClient;

    /**
     * @var HttpMethodsClient
     */
    private static $httpClient;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$mockClient = new \Http\Mock\Client();

        self::$httpClient = new HttpMethodsClient(
            new PluginClient(self::$mockClient, [
                new ErrorPlugin(),
            ]),
            MessageFactoryDiscovery::find()
        );
    }

    /**
     * @throws \Http\Client\Exception
     * @throws JsonMapper_Exception
     */
    public function testGetMarketOrders()
    {
        $json = [
            'status' => 'ok',
            'orders' =>
                [
                    [
                        'unmatchedAmount' => '0.1416000000',
                        'amount' => '0.1416000000',
                        'srcCurrency' => 'Bitcoin',
                        'dstCurrency' => 'Tether(omni)',
                        'matchedAmount' => '0E-10',
                        'isMyOrder' => false,
                        'price' => '5787.0000000000',
                        'type' => 'sell',
                        'totalPrice' => '819.43920000000000000000',
                    ],
                ],
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json)));

        $client = new Client(self::$httpClient, new JsonMapper());

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
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client) {
                $client->getMarketOrders();
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response('200', [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ])));
        $this->assertThrows(
            Exception::class,
            function () use ($client) {
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
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client) {
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

        self::$mockClient->addResponse(new Response('200', [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ])));
        $this->assertThrows(
            Exception::class,
            function () use ($client) {
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
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client) {
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

        self::$mockClient->addResponse(new Response('200', [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ])));
        $this->assertThrows(
            Exception::class,
            function () use ($client) {
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
        $json = [
            'status' => 'ok',
            'profile' =>
                [
                    'firstName' => 'Milad',
                    'lastName' => 'Nekofar',
                    'nationalCode' => '011122333',
                    'email' => 'name@example.com',
                    'username' => 'name@example.com',
                    'phone' => '02142719000-9012',
                    'mobile' => '09151111111',
                    'city' => 'Esfahan',
                    'bankCards' =>
                        [
                            [
                                'number' => '6037-9900-0000-0000',
                                'bank' => 'Melli',
                                'owner' => 'Milad Nekofar',
                                'confirmed' => true,
                                'status' => 'confirmed',
                            ],
                        ],
                    'bankAccounts' =>
                        [
                            [
                                'id' => 1999,
                                'number' => '0346666666666',
                                'shaba' => 'IR460170000000346666666666',
                                'bank' => 'Melli',
                                'owner' => 'Milad Nekofar',
                                'confirmed' => true,
                                'status' => 'confirmed',
                            ],
                        ],
                    'verifications' =>
                        [
                            'email' => true,
                            'phone' => true,
                            'mobile' => true,
                            'identity' => true,
                            'selfie' => false,
                            'bankAccount' => true,
                            'bankCard' => true,
                            'address' => true,
                            'city' => true,
                        ],
                    'pendingVerifications' =>
                        [
                            'email' => false,
                            'phone' => false,
                            'mobile' => false,
                            'identity' => false,
                            'selfie' => false,
                            'bankAccount' => false,
                            'bankCard' => false,
                        ],
                    'options' =>
                        [
                            'fee' => '0.35',
                            'feeUsdt' => '0.2',
                            'isManualFee' => false,
                            'tfa' => false,
                            'socialLoginEnabled' => false,
                        ],
                    'withdrawEligible' => true,
                ],
            'tradeStats' =>
                [
                    'monthTradesTotal' => '10867181.5365000000',
                    'monthTradesCount' => 3,
                ],
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $profile = $client->getUserProfile();

        $this->assertIsObject($profile);
        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertContainsOnlyInstancesOf(Card::class, $profile->cards);
        $this->assertContainsOnlyInstancesOf(Account::class, $profile->accounts);
    }

    public function testGetUserProfileFailure()
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client) {
                $client->getUserProfile();
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response('200', [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ])));
        $this->assertThrows(
            Exception::class,
            function () use ($client) {
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
        $json = [
            'status' => 'ok',
            'attempts' =>
                [
                    [
                        'ip' => '46.209.130.106',
                        'username' => 'name@example.com',
                        'status' => 'Successful',
                        'createdAt' => '2018-11-28T14:16:08.264308+00:00',
                    ],
                ],
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $attempts = $client->getUserLoginAttempts();

        $this->assertIsArray($attempts);
        $this->assertNotEmpty($attempts);
    }

    /**
     *
     */
    public function testGetUserLoginAttemptsFailure()
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client) {
                $client->getUserLoginAttempts();
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response('200', [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ])));
        $this->assertThrows(
            Exception::class,
            function () use ($client) {
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
        $json = [
            'status' => 'ok',
            'referredUsersCount' => 0,
            'referralCode' => '84440',
            'referralFeeTotalCount' => 0,
            'referralFeeTotal' => 0,
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $referralCode = $client->getUserReferralCode();

        $this->assertIsString($referralCode);
        $this->assertNotEmpty($referralCode);
    }

    /**
     *
     */
    public function testGetUserReferralCodeFailure()
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client) {
                $client->getUserReferralCode();
            },
            function ($exception) {
                /** @var Exception $exception */
                $this->assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response('200', [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ])));
        $this->assertThrows(
            Exception::class,
            function () use ($client) {
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
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response('200', [], json_encode(['status' => 'ok'])));
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
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(200));
        $this->assertFalse($client->addUserCard([
            "number" => "5041721011111111",
            "bank" => "Resalat",
        ]));

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            Exception::class,
            function () use ($client) {
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

        self::$mockClient->addResponse(new Response('200', [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ])));
        $this->assertThrows(
            Exception::class,
            function () use ($client) {
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
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response('200', [], json_encode(['status' => 'ok'])));
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
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(200));
        $this->assertFalse($client->addUserAccount([
            "number" => "5041721011111111",
            "bank" => "Resalat",
            "shaba" => "IR111111111111111111111111",
        ]));

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            Exception::class,
            function () use ($client) {
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

        self::$mockClient->addResponse(new Response('200', [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ])));
        $this->assertThrows(
            Exception::class,
            function () use ($client) {
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

}
