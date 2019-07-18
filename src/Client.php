<?php
/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

namespace Nekofar\Nobitex;

use Exception;
use Http\Client\Common\HttpMethodsClient;
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
    private $httpClient;

    /**
     * @var JsonMapper
     */
    private $jsonMapper;

    /**
     * Client constructor.
     *
     * @param HttpMethodsClient $http
     * @param JsonMapper $mapper
     */
    public function __construct(HttpMethodsClient $http, JsonMapper $mapper)
    {
        $this->httpClient = $http;
        $this->jsonMapper = $mapper;
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
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getMarketOrders($params = [])
    {
        $orders = [];
        $body = json_encode($params);

        $response = $this->httpClient->post('/market/orders/list', [], $body);

        $json = json_decode($response->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->orders) && $json->status === 'ok') {
            $orders = $this->jsonMapper
                ->mapArray($json->orders, [], Order::class);
        }

        return $orders;
    }

    /**
     * @param array $params
     *
     * @return Trade[]
     *
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getMarketTrades(array $params)
    {
        if (!isset($params['srcCurrency']) ||
            empty($params['srcCurrency'])) {
            throw new Exception("Source currency is missing.");
        }

        if (!isset($params['dstCurrency']) ||
            empty($params['dstCurrency'])) {
            throw new Exception("Destination currency is missing.");
        }

        $trades = [];
        $body = json_encode($params);

        $response = $this->httpClient->post('/market/trades/list', [], $body);

        $json = json_decode($response->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->trades) && $json->status === 'ok') {
            $trades = $this->jsonMapper
                ->mapArray($json->trades, [], Trade::class);
        }

        return $trades;
    }

    /**
     * @param array $params
     *
     * @return array
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getMarketStats(array $params)
    {
        if (!isset($params['srcCurrency']) ||
            empty($params['srcCurrency'])) {
            throw new Exception("Source currency is missing.");
        }

        if (!isset($params['dstCurrency']) ||
            empty($params['dstCurrency'])) {
            throw new Exception("Destination currency is missing.");
        }

        $stats = [];
        $body = json_encode($params);

        $response = $this->httpClient->post('/market/stats', [], $body);

        $json = json_decode($response->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->stats) && $json->status === 'ok') {
            $match = "{$params['srcCurrency']}-{$params['dstCurrency']}";
            $stats = (array)$json->stats->{$match};
        }

        return $stats;
    }

    /**
     * @return Profile
     *
     * @throws \Http\Client\Exception
     * @throws JsonMapper_Exception
     * @throws Exception
     */
    public function getUserProfile()
    {
        $profile = new Profile();

        $response = $this->httpClient->post('/users/profile');

        $json = json_decode($response->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->profile) && $json->status === 'ok') {
            $this->jsonMapper->undefinedPropertyHandler = [
                Profile::class,
                'setUndefinedProperty',
            ];
            $profile = $this->jsonMapper->map($json->profile, $profile);
        }

        return $profile;
    }

    /**
     * @return array
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getUserLoginAttempts()
    {
        $attempts = [];

        $response = $this->httpClient->post('/users/login-attempts');

        $json = json_decode($response->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->attempts) && $json->status === 'ok') {
            $attempts = (array)$json->attempts;
        }

        return $attempts;
    }

    /**
     * @return string|null
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getUserReferralCode()
    {
        $referralCode = null;

        $response = $this->httpClient->post('/users/get-referral-code');

        $json = json_decode($response->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->referralCode) && $json->status === 'ok') {
            $referralCode = $json->referralCode;
        }

        return $referralCode;
    }

    /**
     * @param array $params
     *
     * @return bool
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function addUserCard(array $params)
    {
        if (!isset($params['bank']) ||
            empty($params['bank'])) {
            throw new Exception("Bank name is missing.");
        }

        if (!isset($params['number']) ||
            !preg_match('/^[0-9]{16}$/', $params['number'])) {
            throw new Exception("Card number is missing.");
        }

        $body = json_encode($params);

        $response = $this->httpClient->post('/users/cards-add', [], $body);

        $json = json_decode($response->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        $json = json_decode($response->getBody());
        if (isset($json->status) && $json->status === 'ok') {
            return true;
        }

        return false;
    }

    /**
     * @param array $params
     *
     * @return bool
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function addUserAccount(array $params)
    {
        if (!isset($params['bank']) ||
            empty($params['bank'])) {
            throw new Exception("Bank name is missing.");
        }

        if (!isset($params['number']) ||
            !preg_match('/^[0-9]+$/', $params['number'])) {
            throw new Exception("Account number is missing.");
        }

        if (!isset($params['shaba']) ||
            !preg_match('/^IR[0-9]{24}$/', $params['shaba'])) {
            throw new Exception("Account shaba is missing.");
        }

        $body = json_encode($params);

        $response = $this->httpClient->post('/users/account-add', [], $body);

        $json = json_decode($response->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        $json = json_decode($response->getBody());
        if (isset($json->status) && $json->status === 'ok') {
            return true;
        }

        return false;
    }
}
