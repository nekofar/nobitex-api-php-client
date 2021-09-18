<?php

/**
 * @package Nekofar\Nobitex
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex;

use Exception;
use GuzzleHttp\Psr7\Response;
use Http\Client\Common\Exception\ClientErrorException;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\MessageFactoryDiscovery;
use InvalidArgumentException;
use Jchook\AssertThrows\AssertThrows;
use JsonMapper;
use JsonMapper_Exception;
use Nekofar\Nobitex\Model\Account;
use Nekofar\Nobitex\Model\Card;
use Nekofar\Nobitex\Model\Deposit;
use Nekofar\Nobitex\Model\Order;
use Nekofar\Nobitex\Model\Profile;
use Nekofar\Nobitex\Model\Trade;
use Nekofar\Nobitex\Model\Transaction;
use Nekofar\Nobitex\Model\Wallet;
use Nekofar\Nobitex\Model\Withdraw;
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
     * @throws \JsonException
     */
    public function testGetMarketOrders(): void
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

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $orders = $client->getMarketOrders([
            "order" => "-price",
            "type" => "sell",
            "dstCurrency" => "usdt"
        ]);

        self::assertIsArray($orders);
        self::assertContainsOnlyInstancesOf(Order::class, $orders);
    }

    /**
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetMarketOrdersFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->getMarketOrders();
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getMarketOrders();
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->getMarketOrders());
    }

    /**
     * @throws \Http\Client\Exception
     * @throws JsonMapper_Exception
     * @throws \JsonException
     */
    public function testGetMarketTrades(): void
    {
        $json = [
            'trades' =>
                [
                    [
                        'srcCurrency' => 'Bitcoin',
                        'dstCurrency' => 'Tether',
                        'timestamp' => '2019-07-17T22:33:18.892390+00:00',
                        'market' => 'BTC-USDT',
                        'price' => '9746.2600000000',
                        'amount' => '0.2765000000',
                        'total' => '2694.84089000000000000000',
                        'type' => 'buy',
                        'fee' => '0E-10',
                    ],
                ],
            'status' => 'ok',
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $trades = $client->getMarketTrades([
            "srcCurrency" => "btc",
            "dstCurrency" => "rls"
        ]);

        self::assertIsArray($trades);
        self::assertContainsOnlyInstancesOf(Trade::class, $trades);
    }

    /**
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetMarketTradesFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->getMarketTrades([
                    "srcCurrency" => "btc",
                    "dstCurrency" => "rls"
                ]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getMarketTrades([
                    "srcCurrency" => "btc",
                    "dstCurrency" => "rls"
                ]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->getMarketTrades([
                    "srcCurrency" => "",
                    "dstCurrency" => "rls"
                ]);
            },
            function ($exception): void {
                /** @var InvalidArgumentException $exception */
                self::assertEquals('Source currency is invalid.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->getMarketTrades([
                    "srcCurrency" => "btc",
                    "dstCurrency" => ""
                ]);
            },
            function ($exception): void {
                /** @var InvalidArgumentException $exception */
                self::assertEquals('Destination currency is invalid.', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->getMarketTrades([
            "srcCurrency" => "btc",
            "dstCurrency" => "rls"
        ]));
    }

    /**
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetMarketStats(): void
    {
        $json = [
            'status' => 'ok',
            'stats' =>
                [
                    'btc-rls' =>
                        [
                            'isClosed' => false,
                            'bestSell' => '1347087999.0000000000',
                            'bestBuy' => '1346846000.0000000000',
                            'volumeSrc' => '20.3949956336',
                            'volumeDst' => '26029041640.3516083664',
                            'latest' => '1341485999.0000000000',
                            'dayLow' => '1182255000.0000000000',
                            'dayHigh' => '1345687000.0000000000',
                            'dayOpen' => '1221790000.0000000000',
                            'dayClose' => '1341485999.0000000000',
                            'dayChange' => '9.80',
                        ],
                ]
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $stats = $client->getMarketStats([
            "srcCurrency" => "btc",
            "dstCurrency" => "rls"
        ]);

        self::assertIsArray($stats);
    }

    /**
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetMarketStatsFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->getMarketStats([
                    "srcCurrency" => "btc",
                    "dstCurrency" => "rls"
                ]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getMarketStats([
                    "srcCurrency" => "btc",
                    "dstCurrency" => "rls"
                ]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->getMarketStats([
                    "srcCurrency" => "",
                    "dstCurrency" => "rls"
                ]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Source currency is invalid.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->getMarketStats([
                    "srcCurrency" => "btc",
                    "dstCurrency" => ""
                ]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Destination currency is invalid.', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->getMarketStats([
            "srcCurrency" => "btc",
            "dstCurrency" => "rls"
        ]));
    }

    /**
     * @throws \Http\Client\Exception
     * @throws JsonMapper_Exception
     * @throws \JsonException
     */
    public function testGetUserProfile(): void
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

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $profile = $client->getUserProfile();

        self::assertIsObject($profile);
        self::assertContainsOnlyInstancesOf(Card::class, $profile->cards);
        self::assertContainsOnlyInstancesOf(Account::class, $profile->accounts);
    }

    /**
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserProfileFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->getUserProfile();
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getUserProfile();
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->getUserProfile());
    }

    /**
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserLoginAttempts(): void
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

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $attempts = $client->getUserLoginAttempts();

        self::assertIsArray($attempts);
    }

    /**
     *
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserLoginAttemptsFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->getUserLoginAttempts();
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getUserLoginAttempts();
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->getUserLoginAttempts());
    }

    /**
     *
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserReferralCode(): void
    {
        $json = [
            'status' => 'ok',
            'referredUsersCount' => 0,
            'referralCode' => '84440',
            'referralFeeTotalCount' => 0,
            'referralFeeTotal' => 0,
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $referralCode = $client->getUserReferralCode();

        self::assertIsString($referralCode);
    }

    /**
     *
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserReferralCodeFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->getUserReferralCode();
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getUserReferralCode();
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->getUserReferralCode());
    }

    /**
     *
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testAddUserCard(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(200, [], json_encode(['status' => 'ok'], JSON_THROW_ON_ERROR)));
        $status = $client->addUserCard([
            "number" => "5041721011111111",
            "bank" => "Resalat"
        ]);

        self::assertTrue($status);
    }

    /**
     *
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testAddUserCardFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->addUserCard([
            "number" => "5041721011111111",
            "bank" => "Resalat",
        ]));

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->addUserCard([
                    "number" => "5041721011111111",
                    "bank" => "Resalat",
                ]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->addUserCard([
                    "number" => "5041721011111111",
                    "bank" => "Resalat",
                ]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->addUserCard([
                    "number" => "",
                    "bank" => "5041721011111111",
                ]);
            },
            function ($exception): void {
                /** @var InvalidArgumentException $exception */
                self::assertEquals('Card number is invalid.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->addUserCard([
                    "number" => "50417210111111111",
                    "bank" => "",
                ]);
            },
            function ($exception): void {
                /** @var InvalidArgumentException $exception */
                self::assertEquals('Bank name is invalid.', $exception->getMessage());
            }
        );
    }

    /**
     *
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testAddUserAccount(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(200, [], json_encode(['status' => 'ok'], JSON_THROW_ON_ERROR)));
        $status = $client->addUserAccount([
            "number" => "5041721011111111",
            "bank" => "Resalat",
            "shaba" => "IR111111111111111111111111",
        ]);

        self::assertTrue($status);
    }

    /**
     *
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testAddUserAccountFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->addUserAccount([
            "number" => "5041721011111111",
            "bank" => "Resalat",
            "shaba" => "IR111111111111111111111111",
        ]));

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->addUserAccount([
                    "number" => "5041721011111111",
                    "bank" => "Resalat",
                    "shaba" => "IR111111111111111111111111",
                ]);
            },
            function ($exception): void {
                /** @var ClientErrorException $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->addUserAccount([
                    "number" => "5041721011111111",
                    "bank" => "Resalat",
                    "shaba" => "IR111111111111111111111111",
                ]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );
        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->addUserAccount([
                    "number" => "",
                    "bank" => "5041721011111111",
                    "shaba" => "IR111111111111111111111111",
                ]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Account number is invalid.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->addUserAccount([
                    "number" => "50417210111111111",
                    "bank" => "",
                    "shaba" => "IR111111111111111111111111",
                ]);
            },
            function ($exception): void {
                /** @var InvalidArgumentException $exception */
                self::assertEquals('Bank name is invalid.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->addUserAccount([
                    "number" => "50417210111111111",
                    "bank" => "Resalat",
                    "shaba" => "IR1111111111111111111111110",
                ]);
            },
            function ($exception): void {
                /** @var InvalidArgumentException $exception */
                self::assertEquals('Account shaba is invalid.', $exception->getMessage());
            }
        );
    }

    /**
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserLimitations(): void
    {
        $json = [
            'status' => 'ok',
            'limitations' =>
                [
                    'userLevel' => 'level2',
                    'features' =>
                        [
                            'crypto_trade' => false,
                            'rial_trade' => false,
                            'coin_deposit' => false,
                            'rial_deposit' => false,
                            'coin_withdrawal' => false,
                            'rial_withdrawal' => false,
                        ],
                    'limits' =>
                        [
                            'withdrawRialDaily' =>
                                [
                                    'used' => '0',
                                    'limit' => '900000000',
                                ],
                            'withdrawCoinDaily' =>
                                [
                                    'used' => '0',
                                    'limit' => '2000000000',
                                ],
                            'withdrawTotalDaily' =>
                                [
                                    'used' => '0',
                                    'limit' => '2000000000',
                                ],
                            'withdrawTotalMonthly' =>
                                [
                                    'used' => '0',
                                    'limit' => '30000000000',
                                ],
                        ],
                ],
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $limitations = $client->getUserLimitations();

        self::assertIsArray($limitations);
    }

    /**
     *
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserLimitationsFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->getUserLimitations();
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getUserLimitations();
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->getUserLimitations());
    }

    /**
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserWallets(): void
    {
        $json = [
            'status' => 'ok',
            'wallets' =>
                [
                    [
                        'activeBalance' => '10.2649975000',
                        'blockedBalance' => '0',
                        'user' => 'name@example.com',
                        'currency' => 'ltc',
                        'id' => 4159,
                        'balance' => '10.2649975000',
                        'rialBalance' => 51322935,
                        'rialBalanceSell' => 52507310,
                        'depositAddress' => null,
                    ],
                ],
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $wallets = $client->getUserWallets();

        self::assertIsArray($wallets);
        self::assertContainsOnlyInstancesOf(Wallet::class, $wallets);
    }

    /**
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserWalletsFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->getUserWallets();
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getUserWallets();
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->getUserWallets());
    }

    /**
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserWalletBalance(): void
    {
        $json = [
            'balance' => '10.2649975000',
            'status' => 'ok',
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $balance = $client->getUserWalletBalance(['currency' => 'ltc']);

        self::assertIsFloat($balance);
    }

    /**
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserWalletBalanceFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->getUserWalletBalance(['currency' => 'ltc']);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getUserWalletBalance(['currency' => 'ltc']);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->getUserWalletBalance(['currency' => '']);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Currency code is invalid.', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->getUserWalletBalance(['currency' => 'ltc']));

    }

    /**
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserWalletTransactions(): void
    {
        $json = [
            'transactions' =>
                [
                    [
                        'currency' => 'ltc',
                        'created_at' => '2018-10-04T13:05:01.384902+00:00',
                        'calculatedFee' => '0',
                        'id' => 96541,
                        'amount' => '-1.0000000000',
                        'description' => 'Withdraw to "Lgn1zc77mEjk72KvXPqyXq8K1mAfcDE6YR"',
                    ],
                ],
            'status' => 'ok',
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $transactions = $client->getUserWalletTransactions(['wallet' => 123456]);

        self::assertIsArray($transactions);
        self::assertContainsOnlyInstancesOf(Transaction::class, $transactions);
    }

    /**
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserWalletTransactionsFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->getUserWalletTransactions(['wallet' => 123456]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getUserWalletTransactions(['wallet' => 123456]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->getUserWalletTransactions(['wallet' => 0]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Wallet id is invalid.', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->getUserWalletTransactions(['wallet' => 123456]));

    }

    /**
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserWalletDeposits(): void
    {
        $json = [
            'status' => 'ok',
            'deposits' =>
                [
                    0 =>
                        [
                            'txHash' => 'c5d84268a0bf02307b5a0460a68b61987a9b3009d3a82a817e41558e619ec1d2',
                            'address' => '32KfyTNh162UoKithfDrWHZPYq5uePGmf7',
                            'confirmed' => true,
                            'transaction' =>
                                [
                                    'id' => 10,
                                    'amount' => '3.0000000000',
                                    'currency' => 'btc',
                                    'description' => 'Deposit - address:36n452uGq1x4mK7bfyZR8wgE47AnBb2pzi, tx:c5d84268a0bf02307b5a0460a68b61987a9b3009d3a82a817e41558e619ec1d2',
                                    'created_at' => '2018-11-06T03:56:18+00:00',
                                    'calculatedFee' => '0',
                                ],
                            'currency' => 'Bitcoin',
                            'blockchainUrl' => 'https://btc.com/c5d84268a0bf02307b5a0460a68b61987a9b3009d3a82a817e41558e619ec1d2',
                            'confirmations' => 2,
                            'requiredConfirmations' => 3,
                            'amount' => '3.0000000000',
                        ],
                ],
            'withdraws' =>
                [
                    0 =>
                        [
                            'id' => 2398,
                            'blockchain_url' => 'https://live.blockcypher.com/ltc/tx/c1ed4229e598d4cf81e99e79fb06294a70af39443e2639e22c69bc30d6ecda67/',
                            'is_cancelable' => false,
                            'status' => 'Done',
                            'amount' => '1.0000000000',
                            'createdAt' => '2018-10-04T12:59:38.196935+00:00',
                            'wallet_id' => 4159,
                            'currency' => 'ltc',
                            'address' => 'Lgn1zc77mEjk72KvXPqyXq8K1mAfcDE6YR',
                        ],
                ],
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $deposits = $client->getUserWalletDeposits(['wallet' => 123456]);

        self::assertIsArray($deposits);
        self::assertContainsOnlyInstancesOf(Deposit::class, $deposits);
    }

    /**
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserWalletDepositsFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->getUserWalletDeposits(['wallet' => 123456]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getUserWalletDeposits(['wallet' => 123456]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->getUserWalletDeposits(['wallet' => 0]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Wallet id is invalid.', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->getUserWalletDeposits(['wallet' => 123456]));

    }

    /**
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserWalletWithdraws(): void
    {
        $json = [
            'status' => 'ok',
            'deposits' =>
                [
                    0 =>
                        [
                            'txHash' => 'c5d84268a0bf02307b5a0460a68b61987a9b3009d3a82a817e41558e619ec1d2',
                            'address' => '32KfyTNh162UoKithfDrWHZPYq5uePGmf7',
                            'confirmed' => true,
                            'transaction' =>
                                [
                                    'id' => 10,
                                    'amount' => '3.0000000000',
                                    'currency' => 'btc',
                                    'description' => 'Deposit - address:36n452uGq1x4mK7bfyZR8wgE47AnBb2pzi, tx:c5d84268a0bf02307b5a0460a68b61987a9b3009d3a82a817e41558e619ec1d2',
                                    'created_at' => '2018-11-06T03:56:18+00:00',
                                    'calculatedFee' => '0',
                                ],
                            'currency' => 'Bitcoin',
                            'blockchainUrl' => 'https://btc.com/c5d84268a0bf02307b5a0460a68b61987a9b3009d3a82a817e41558e619ec1d2',
                            'confirmations' => 2,
                            'requiredConfirmations' => 3,
                            'amount' => '3.0000000000',
                        ],
                ],
            'withdraws' =>
                [
                    0 =>
                        [
                            'id' => 2398,
                            'blockchain_url' => 'https://live.blockcypher.com/ltc/tx/c1ed4229e598d4cf81e99e79fb06294a70af39443e2639e22c69bc30d6ecda67/',
                            'is_cancelable' => false,
                            'status' => 'Done',
                            'amount' => '1.0000000000',
                            'createdAt' => '2018-10-04T12:59:38.196935+00:00',
                            'wallet_id' => 4159,
                            'currency' => 'ltc',
                            'address' => 'Lgn1zc77mEjk72KvXPqyXq8K1mAfcDE6YR',
                        ],
                ],
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $withdraws = $client->getUserWalletWithdraws(['wallet' => 123456]);

        self::assertIsArray($withdraws);
        self::assertContainsOnlyInstancesOf(Withdraw::class, $withdraws);
    }

    /**
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserWalletWithdrawsFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->getUserWalletWithdraws(['wallet' => 123456]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getUserWalletWithdraws(['wallet' => 123456]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->getUserWalletWithdraws(['wallet' => 0]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Wallet id is invalid.', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->getUserWalletWithdraws(['wallet' => 123456]));

    }

    /**
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserWalletAddress(): void
    {
        $json = [
            'status' => 'ok',
            'address' => 'rwRmyGRoJkHKtojaC8SH2wxsnB2q3yNopB',
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $address = $client->getUserWalletAddress(['wallet' => '123456']);

        self::assertNotEmpty($address);
    }

    /**
     *
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetUserWalletAddressFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(200));
        $client->getUserWalletAddress(['wallet' => '123456']);

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getUserWalletAddress(['wallet' => '123456']);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getUserWalletAddress(['wallet' => '123456']);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->getUserWalletAddress(['wallet' => 0]);
            },
            function ($exception): void {
                /** @var InvalidArgumentException $exception */
                self::assertEquals('Wallet id is invalid.', $exception->getMessage());
            }
        );
    }

    /**
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testAddMarketOrder(): void
    {
        $json = [
            'status' => 'ok',
            'order' =>
                [
                    'type' => 'sell',
                    'srcCurrency' => 'Bitcoin',
                    'dstCurrency' => 'ریال',
                    'price' => '520000000',
                    'amount' => '0.6',
                    'totalPrice' => '312000000.0',
                    'matchedAmount' => 0,
                    'unmatchedAmount' => '0.6',
                    'isMyOrder' => false,
                    'id' => 25,
                    'status' => 'Active',
                    'partial' => false,
                    'fee' => 0,
                    'user' => 'name@example.com',
                    'created_at' => '2018-11-28T11:36:13.592827+00:00',
                ],
        ];

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $order = $client->addMarketOrder([
            'type' => 'buy',
            'srcCurrency' => 'btc',
            'dstCurrency' => 'rls',
            'amount' => '0.6',
            'price' => 520000000,
        ]);

        self::assertNotFalse($order);
    }

    /**
     *
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testAddMarketOrderFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(200));
        $client->addMarketOrder([
            'type' => 'buy',
            'srcCurrency' => 'btc',
            'dstCurrency' => 'rls',
            'amount' => '0.6',
            'price' => 520000000,
        ]);

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->addMarketOrder([
                    'type' => 'buy',
                    'srcCurrency' => 'btc',
                    'dstCurrency' => 'rls',
                    'amount' => '0.6',
                    'price' => 520000000,
                ]);
            },
            function ($exception): void {
                /** @var ClientErrorException $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->addMarketOrder([
                    'type' => 'buy',
                    'srcCurrency' => 'btc',
                    'dstCurrency' => 'rls',
                    'amount' => '0.6',
                    'price' => 520000000,
                ]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->addMarketOrder([
                    'type' => '',
                    'srcCurrency' => 'btc',
                    'dstCurrency' => 'rls',
                    'amount' => '0.6',
                    'price' => 520000000,
                ]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Order type is invalid.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->addMarketOrder([
                    'type' => 'buy',
                    'srcCurrency' => '',
                    'dstCurrency' => 'rls',
                    'amount' => '0.6',
                    'price' => 520000000,
                ]);
            },
            function ($exception): void {
                /** @var InvalidArgumentException $exception */
                self::assertEquals('Source currency is invalid.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->addMarketOrder([
                    'type' => 'buy',
                    'srcCurrency' => 'btc',
                    'dstCurrency' => '',
                    'amount' => '0.6',
                    'price' => 520000000,
                ]);
            },
            function ($exception): void {
                /** @var InvalidArgumentException $exception */
                self::assertEquals('Destination currency is invalid.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->addMarketOrder([
                    'type' => 'buy',
                    'srcCurrency' => 'btc',
                    'dstCurrency' => 'rls',
                    'amount' => '',
                    'price' => 520000000,
                ]);
            },
            function ($exception): void {
                /** @var InvalidArgumentException $exception */
                self::assertEquals('Order amount is invalid.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->addMarketOrder([
                    'type' => 'buy',
                    'srcCurrency' => 'btc',
                    'dstCurrency' => 'rls',
                    'amount' => '0.6',
                    'price' => 0,
                ]);
            },
            function ($exception): void {
                /** @var InvalidArgumentException $exception */
                self::assertEquals('Order price is invalid.', $exception->getMessage());
            }
        );
    }

    /**
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetMarketOrder(): void
    {
        $json = array(
            'status' => 'ok',
            'order' =>
                array(
                    'unmatchedAmount' => '3.0000000000',
                    'fee' => '0E-10',
                    'matchedAmount' => '0E-10',
                    'partial' => false,
                    'price' => '8500000.0000000000',
                    'created_at' => '2018-11-28T12:25:22.696029+00:00',
                    'user' => 'name@example.com',
                    'id' => 5684,
                    'srcCurrency' => 'Litecoin',
                    'totalPrice' => '25500000.00000000000000000000',
                    'type' => 'sell',
                    'dstCurrency' => '﷼',
                    'isMyOrder' => false,
                    'status' => 'Active',
                    'amount' => '3.0000000000',
                ),
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        $order = $client->getMarketOrder(['id' => 123456]);

        self::assertNotFalse($order);
    }

    /**
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testGetMarketOrderFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                $client->getMarketOrder(['id' => 123456]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                $client->getMarketOrder(['id' => 123456]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                $client->getMarketOrder(['id' => 0]);
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Order id is invalid.', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200));
        self::assertNull($client->getMarketOrder(['id' => 123456]));
    }

    /**
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testSetMarketOrderStatus(): void
    {
        $json = array(
            'status' => 'ok',
            'updatedStatus' => 'Canceled',
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode($json, JSON_THROW_ON_ERROR)));

        $client = new Client(self::$httpClient, new JsonMapper());

        self::assertTrue($client->setMarketOrderStatus(['order' => 123456, 'status' => 'canceled']));
    }

    /**
     * @throws \Http\Client\Exception
     * @throws \JsonException
     */
    public function testSetMarketOrderStatusFailure(): void
    {
        $client = new Client(self::$httpClient, new JsonMapper());

        self::$mockClient->addResponse(new Response(401));
        $this->assertThrows(
            ClientErrorException::class,
            function () use ($client): void {
                self::assertTrue($client->setMarketOrderStatus(['order' => 123456, 'status' => 'canceled']));
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Unauthorized', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200, [], json_encode([
            'status' => 'failed',
            'message' => 'Validation Failed'
        ], JSON_THROW_ON_ERROR)));
        $this->assertThrows(
            Exception::class,
            function () use ($client): void {
                self::assertTrue($client->setMarketOrderStatus(['order' => 123456, 'status' => 'canceled']));
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Validation Failed', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                self::assertTrue($client->setMarketOrderStatus(['order' => 0, 'status' => 'canceled']));
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Order id is invalid.', $exception->getMessage());
            }
        );

        $this->assertThrows(
            InvalidArgumentException::class,
            function () use ($client): void {
                self::assertTrue($client->setMarketOrderStatus(['order' => 123456, 'status' => '']));
            },
            function ($exception): void {
                /** @var Exception $exception */
                self::assertEquals('Order status is invalid.', $exception->getMessage());
            }
        );

        self::$mockClient->addResponse(new Response(200));
        self::assertFalse($client->setMarketOrderStatus(['order' => 123456, 'status' => 'canceled']));
    }

}
