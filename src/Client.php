<?php
/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

namespace Nekofar\Nobitex;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Exception;
use Http\Client\HttpClient;
use JsonMapper;
use JsonMapper_Exception;
use Nekofar\Nobitex\Model\Order;
use Nekofar\Nobitex\Model\Profile;
use Nekofar\Nobitex\Model\Trade;

/**
 * Class Client
 */
class Client
{

    /**
     * @var HttpClient
     */
    private $http;

    /**
     * @var JsonMapper
     */
    private $mapper;

    /**
     * Client constructor.
     *
     * @param HttpMethodsClient $http
     * @param JsonMapper $mapper
     */
    public function __construct(HttpMethodsClient $http, JsonMapper $mapper)
    {
        $this->http = $http;
        $this->mapper = $mapper;
    }

    /**
     * @param Config $config
     *
     * @return Client
     */
    public static function create(Config $config)
    {
        return new static(
            $config->createHttpClient(),
            $config->createJsonMapper()
        );
    }

    /**
     * @param array $params
     *
     * @return Order[]
     *
     * @throws Exception
     * @throws JsonMapper_Exception
     */
    public function getMarketOrders(array $params)
    {
        $orders = [];
        $params = $params + [
                'order' => 'price',
                'type' => null,
                'srcCurrency' => null,
                'dstCurrency' => null,
            ];

        $response = $this->http->post(
            Config::DEFAULT_API_URL . '/market/orders/list',
            [],
            json_encode($params)
        );

        if ($response->getStatusCode() === 200) {
            $json = json_decode($response->getBody());
            if (isset($json->orders)) {
                $orders = $this->mapper
                    ->mapArray(
                        $json->orders,
                        [],
                        'Nekofar\Nobitex\Model\Order'
                    );
            }
        }

        return $orders;
    }

    /**
     * @param array $params
     *
     * @return Trade[]
     *
     * @throws Exception
     * @throws JsonMapper_Exception
     */
    public function getMarketTrades(array $params)
    {
        $trades = [];
        $params = $params + [
                'srcCurrency' => 'btc',
                'dstCurrency' => 'rls',
            ];

        $response = $this->http->post(
            Config::DEFAULT_API_URL . '/market/trades/list',
            [],
            json_encode($params)
        );

        if ($response->getStatusCode() === 200) {
            $json = json_decode($response->getBody());
            if (isset($json->trades)) {
                $trades = $this->mapper->mapArray(
                    $json->trades,
                    [],
                    Trade::class
                );
            }
        }

        return $trades;
    }

    /**
     * @param array $params
     *
     * @return array
     *
     * @throws Exception
     */
    public function getMarketStats(array $params)
    {
        $stats = [];
        $params = $params + ['srcCurrency' => 'btc', 'dstCurrency' => 'rls'];

        $response = $this->http->post(
            Config::DEFAULT_API_URL . '/market/stats',
            [],
            json_encode($params)
        );

        if ($response->getStatusCode() === 200) {
            $json = json_decode($response->getBody());
            if (isset($json->stats)) {
                $stats = $json->stats
                    ->{"{$params['srcCurrency']}-{$params['dstCurrency']}"};
            }
        }

        return $stats;
    }

    /**
     * @return Profile
     *
     * @throws Exception
     * @throws JsonMapper_Exception
     */
    public function getUserProfile()
    {
        $profile = new Profile();

        $response = $this->http->post(
            Config::DEFAULT_API_URL . '/users/profile'
        );

        if ($response->getStatusCode() === 200) {
            $json = json_decode($response->getBody());
            if (isset($json->profile)) {
                $this->mapper->undefinedPropertyHandler = [
                    Profile::class,
                    'setUndefinedProperty',
                ];
                $profile = $this->mapper->map($json->profile, $profile);
            }
        }

        return $profile;
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public function getUserLoginAttempts()
    {
        $attempts = [];

        $response = $this->http->post(
            Config::DEFAULT_API_URL . '/users/login-attempts'
        );

        if ($response->getStatusCode() === 200) {
            $json = json_decode($response->getBody(), true);
            if (isset($json['attempts'])) {
                $attempts = $json['attempts'];
            }
        }

        return $attempts;
    }

    /**
     * @return string|null
     *
     * @throws Exception
     */
    public function getUserReferralCode()
    {
        $referralCode = null;

        $response = $this->http->post(
            Config::DEFAULT_API_URL . '/users/get-referral-code'
        );

        if ($response->getStatusCode() === 200) {
            $json = json_decode($response->getBody(), true);
            if (isset($json['referralCode'])) {
                $referralCode = $json['referralCode'];
            }
        }

        return $referralCode;
    }

    /**
     * @param array $params
     *
     * @return bool
     *
     * @throws Exception
     * @throws \Exception
     */
    public function addUserCard(array $params)
    {
        if (!isset($params['bank']) ||
            empty($params['bank'])) {
            throw new \Exception("Bank name is missing.");
        }

        if (!isset($params['number']) ||
            preg_match('/^[0-9]{16}$/', $params['number']) === false) {
            throw new \Exception("Card number is missing.");
        }

        $response = $this->http->post(
            Config::DEFAULT_API_URL . '/users/cards-add',
            [],
            json_encode($params)
        );

        if ($response->getStatusCode() === 200) {
            $json = json_decode($response->getBody(), true);
            if (isset($json['status']) && $json['status'] === 'ok') {
                return true;
            }
        }

        return false;
    }

}
