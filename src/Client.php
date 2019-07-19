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
use InvalidArgumentException;
use JsonMapper;
use JsonMapper_Exception;
use Nekofar\Nobitex\Model\Deposit;
use Nekofar\Nobitex\Model\Order;
use Nekofar\Nobitex\Model\Profile;
use Nekofar\Nobitex\Model\Trade;
use Nekofar\Nobitex\Model\Transaction;
use Nekofar\Nobitex\Model\Wallet;
use Nekofar\Nobitex\Model\Withdraw;

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
     * @param array $args
     *
     * @return Order[]|false Return and array on success or false on
     *                       unexpected errors.
     *
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getMarketOrders($args = [])
    {
        $data = json_encode($args);
        $resp = $this->httpClient->post('/market/orders/list', [], $data);
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->orders) && $json->status === 'ok') {
            return $this->jsonMapper
                ->mapArray($json->orders, [], Order::class);
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return Trade[]|false Return and array on success or false on
     *                       unexpected errors.
     *
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getMarketTrades(array $args)
    {
        if (!isset($args['srcCurrency']) ||
            empty($args['srcCurrency'])) {
            throw new InvalidArgumentException("Source currency is invalid.");
        }

        if (!isset($args['dstCurrency']) ||
            empty($args['dstCurrency'])) {
            throw new InvalidArgumentException("Destination currency is invalid."); // phpcs:ignore
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/market/trades/list', [], $data);
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->trades) && $json->status === 'ok') {
            return $this->jsonMapper
                ->mapArray($json->trades, [], Trade::class);
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return array|false Return an array on success or false on
     *                     unexpected errors.
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getMarketStats(array $args)
    {
        if (!isset($args['srcCurrency']) ||
            empty($args['srcCurrency'])) {
            throw new InvalidArgumentException("Source currency is invalid.");
        }

        if (!isset($args['dstCurrency']) ||
            empty($args['dstCurrency'])) {
            throw new InvalidArgumentException("Destination currency is invalid."); // phpcs:ignore
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/market/stats', [], $data);
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->stats) && $json->status === 'ok') {
            return (array)$json->stats
                ->{"{$args['srcCurrency']}-{$args['dstCurrency']}"};
        }

        return false;
    }

    /**
     * @return Profile|false Return a Profile object on success or false on
     *                       unexpected errors
     *
     * @throws \Http\Client\Exception
     * @throws JsonMapper_Exception
     * @throws Exception
     */
    public function getUserProfile()
    {
        $resp = $this->httpClient->post('/users/profile');
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->profile) && $json->status === 'ok') {
            $this->jsonMapper->undefinedPropertyHandler = [
                Profile::class,
                'setUndefinedProperty',
            ];

            /** @var Profile $profile */
            $profile = $this->jsonMapper->map($json->profile, new Profile());

            return $profile;
        }

        return false;
    }

    /**
     * @return array|false Return an array on success or false on
     *                     unexpected errors.
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getUserLoginAttempts()
    {
        $resp = $this->httpClient->post('/users/login-attempts');
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->attempts) && $json->status === 'ok') {
            return (array)$json->attempts;
        }

        return false;
    }

    /**
     * @return string|false
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getUserReferralCode()
    {
        $resp = $this->httpClient->post('/users/get-referral-code');
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->referralCode) && $json->status === 'ok') {
            return $json->referralCode;
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return bool
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function addUserCard(array $args)
    {
        if (!isset($args['bank']) ||
            empty($args['bank'])) {
            throw new InvalidArgumentException("Bank name is invalid.");
        }

        if (!isset($args['number']) ||
            !preg_match('/^[0-9]{16}$/', $args['number'])) {
            throw new InvalidArgumentException("Card number is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/cards-add', [], $data);
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->status) && $json->status === 'ok') {
            return true;
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return bool
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function addUserAccount(array $args)
    {
        if (!isset($args['bank']) ||
            empty($args['bank'])) {
            throw new InvalidArgumentException("Bank name is invalid.");
        }

        if (!isset($args['number']) ||
            !preg_match('/^[0-9]+$/', $args['number'])) {
            throw new InvalidArgumentException("Account number is invalid.");
        }

        if (!isset($args['shaba']) ||
            !preg_match('/^IR[0-9]{24}$/', $args['shaba'])) {
            throw new InvalidArgumentException("Account shaba is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/account-add', [], $data);
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->status) && $json->status === 'ok') {
            return true;
        }

        return false;
    }

    /**
     * @return array|false Return an array on success or false on
     *                     unexpected errors.
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getUserLimitations()
    {
        $resp = $this->httpClient->post('/users/get-referral-code');
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->limitations) && $json->status === 'ok') {
            return json_decode(json_encode($json->limitations), true);
        }

        return false;
    }

    /**
     * @return Wallet[]|bool
     *
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getUserWallets()
    {
        $resp = $this->httpClient->post('/users/wallets/list');
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->wallets) && $json->status === 'ok') {
            return $this->jsonMapper
                ->mapArray($json->wallets, [], Wallet::class);
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return float|bool
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getUserWalletBalance(array $args)
    {
        if (!isset($args['currency']) ||
            empty($args['currency'])) {
            throw new InvalidArgumentException("Currency code is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/wallets/balance', [], $data);
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->balance) && $json->status === 'ok') {
            return (float)$json->balance;
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return Transaction[]|false
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getUserWalletTransactions(array $args)
    {
        if (!isset($args['wallet']) ||
            empty($args['wallet'])) {
            throw new InvalidArgumentException("Wallet id is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/wallets/transactions/list', [], $data); // phpcs:ignore
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->transactions) && $json->status === 'ok') {
            $this->jsonMapper->undefinedPropertyHandler = [
                Transaction::class,
                'setUndefinedProperty',
            ];

            return $this->jsonMapper
                ->mapArray($json->transactions, [], Transaction::class);
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return Deposit[]|false
     *
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getUserWalletDeposits(array $args)
    {
        if (!isset($args['wallet']) ||
            empty($args['wallet'])) {
            throw new InvalidArgumentException("Wallet id is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/wallets/deposits/list', [], $data); // phpcs:ignore
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->deposits) && $json->status === 'ok') {
            return $this->jsonMapper
                ->mapArray($json->deposits, [], Deposit::class);
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return Withdraw[]|false
     *
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getUserWalletWithdraws(array $args)
    {
        if (!isset($args['wallet']) ||
            empty($args['wallet'])) {
            throw new InvalidArgumentException("Wallet id is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/wallets/deposits/list', [], $data); // phpcs:ignore
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->withdraws) && $json->status === 'ok') {
            $this->jsonMapper->undefinedPropertyHandler = [
                Withdraw::class,
                'setUndefinedProperty',
            ];

            return $this->jsonMapper
                ->mapArray($json->withdraws, [], Withdraw::class);
        }

        return false;
    }
}
