<?php
/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

namespace Nekofar\Nobitex\Client;

use Exception;
use Http\Client\Common\HttpMethodsClient;
use InvalidArgumentException;
use JsonMapper;
use JsonMapper_Exception;
use Nekofar\Nobitex\Model\Deposit;
use Nekofar\Nobitex\Model\Transaction;
use Nekofar\Nobitex\Model\Wallet;
use Nekofar\Nobitex\Model\Withdraw;

/**
 * Trait Wallet
 */
trait WalletTrait
{

    /**
     * @var HttpMethodsClient
     */
    private $httpClient;

    /**
     * @var JsonMapper
     */
    private $jsonMapper;


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

    /**
     * @param array $args
     *
     * @return bool
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getUserWalletAddress(array $args)
    {
        if (!isset($args['wallet']) ||
            empty($args['wallet'])) {
            throw new InvalidArgumentException("Wallet id is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/wallets/generate-address', [], $data); // phpcs:ignore
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->address) && $json->status === 'ok') {
            return $json->address;
        }

        return false;
    }
}
