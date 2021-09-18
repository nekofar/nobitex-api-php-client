<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Client;

use Exception;
use InvalidArgumentException;
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
     * @var \Http\Client\Common\HttpMethodsClient
     */
    private $httpClient;

    /**
     * @var \JsonMapper
     */
    private $jsonMapper;

    /**
     * @return array<\Nekofar\Nobitex\Model\Wallet>|bool
     *
     * @throws \JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \Exception
     */
    public function getUserWallets()
    {
        $resp = $this->httpClient->post('/users/wallets/list');
        $json = json_decode((string) $resp->getBody());

        if (property_exists($json, 'message') && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (property_exists($json, 'wallets') && 'ok' === $json->status) {
            return $this->jsonMapper
                ->mapArray($json->wallets, [], Wallet::class);
        }

        return false;
    }

    /**
     * @param array<string, integer|string> $args
     *
     * @return float|bool
     *
     * @throws \Http\Client\Exception
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function getUserWalletBalance(array $args)
    {
        if (!array_key_exists('currency', $args) || in_array($args['currency'], [null, ''], true)) {
            throw new InvalidArgumentException("Currency code is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/wallets/balance', [], $data);
        $json = json_decode((string) $resp->getBody());

        if (property_exists($json, 'message') && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (property_exists($json, 'balance') && 'ok' === $json->status) {
            return (float) $json->balance;
        }

        return false;
    }

    /**
     * @param array<string, integer|string> $args
     *
     * @return array<\Nekofar\Nobitex\Model\Transaction>|false
     *
     * @throws \Http\Client\Exception
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function getUserWalletTransactions(array $args)
    {
        if (!array_key_exists('wallet', $args) || in_array($args['wallet'], [null, ''], true)) {
            throw new InvalidArgumentException("Wallet id is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/wallets/transactions/list', [], $data); // phpcs:ignore
        $json = json_decode((string) $resp->getBody());

        if (property_exists($json, 'message') && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (property_exists($json, 'transactions') && 'ok' === $json->status) {
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
     * @param array<string, integer|string> $args
     *
     * @return array<\Nekofar\Nobitex\Model\Deposit>|false
     *
     * @throws \JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function getUserWalletDeposits(array $args)
    {
        if (!array_key_exists('wallet', $args) || in_array($args['wallet'], [null, ''], true)) {
            throw new InvalidArgumentException("Wallet id is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/wallets/deposits/list', [], $data); // phpcs:ignore
        $json = json_decode((string) $resp->getBody());

        if (property_exists($json, 'message') && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (property_exists($json, 'deposits') && 'ok' === $json->status) {
            return $this->jsonMapper
                ->mapArray($json->deposits, [], Deposit::class);
        }

        return false;
    }

    /**
     * @param array<string, integer|string> $args
     *
     * @return array<\Nekofar\Nobitex\Model\Withdraw>|false
     *
     * @throws \JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function getUserWalletWithdraws(array $args)
    {
        if (!array_key_exists('wallet', $args) || in_array($args['wallet'], [null, ''], true)) {
            throw new InvalidArgumentException("Wallet id is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/wallets/deposits/list', [], $data); // phpcs:ignore
        $json = json_decode((string) $resp->getBody());

        if (property_exists($json, 'message') && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (property_exists($json, 'withdraws') && 'ok' === $json->status) {
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
     * @param array<string, integer|string> $args
     *
     * @throws \Http\Client\Exception
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function getUserWalletAddress(array $args): bool
    {
        if (!array_key_exists('wallet', $args) || in_array($args['wallet'], [null, ''], true)) {
            throw new InvalidArgumentException("Wallet id is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/wallets/generate-address', [], $data); // phpcs:ignore
        $json = json_decode((string) $resp->getBody());

        if (property_exists($json, 'message') && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (property_exists($json, 'address') && 'ok' === $json->status) {
            return $json->address;
        }

        return false;
    }
}
