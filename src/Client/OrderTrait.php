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
use Nekofar\Nobitex\Model\Order;

/**
 * Trait OrderTrait
 */
trait OrderTrait
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
     * Return and array on success or false on unexpected errors.
     *
     * @param array<string, integer|string> $args
     *
     * @return array<\Nekofar\Nobitex\Model\Order>|false
     *
     * @throws \JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \Exception
     */
    public function getMarketOrders(array $args = [])
    {
        $data = json_encode($args);
        $resp = $this->httpClient->post('/market/orders/list', [], $data);
        $json = json_decode((string) $resp->getBody());

        if (isset($json->message) && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (isset($json->orders) && 'ok' === $json->status) {
            return $this->jsonMapper
                ->mapArray($json->orders, [], Order::class);
        }

        return false;
    }

    /**
     * @param array<string, integer|string> $args
     *
     * @return \Nekofar\Nobitex\Model\Order|false
     *
     * @throws \Http\Client\Exception
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function addMarketOrder(array $args)
    {
        if (!array_key_exists('type', $args) || in_array($args['type'], [null, ''], true)) {
            throw new InvalidArgumentException("Order type is invalid.");
        }

        if (!array_key_exists('srcCurrency', $args) || in_array($args['srcCurrency'], [null, ''], true)) {
            throw new InvalidArgumentException("Source currency is invalid.");
        }

        if (!array_key_exists('dstCurrency', $args) || in_array($args['dstCurrency'], [null, ''], true)) {
            throw new InvalidArgumentException("Destination currency is invalid."); // phpcs:ignore
        }

        if (!array_key_exists('amount', $args) || in_array($args['amount'], [null, ''], true)) {
            throw new InvalidArgumentException("Order amount is invalid.");
        }

        if (!array_key_exists('price', $args) || in_array($args['price'], [null, '', 0], true)) {
            throw new InvalidArgumentException("Order price is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/market/orders/add', [], $data);
        $json = json_decode((string) $resp->getBody());

        if (isset($json->message) && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (isset($json->order) && 'ok' === $json->status) {
            /** @var \Nekofar\Nobitex\Model\Order $order */
            $order = $this->jsonMapper->map($json->order, new Order());

            return $order;
        }

        return false;
    }

    /**
     * @param array<string, integer|string> $args
     *
     * @return \Nekofar\Nobitex\Client\OrderTrait|false
     *
     * @throws \JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \Exception
     */
    public function getMarketOrder(array $args)
    {
        if (!array_key_exists('id', $args) || in_array($args['id'], [null, '', 0], true)) {
            throw new InvalidArgumentException("Order id is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/market/orders/status', [], $data);
        $json = json_decode((string) $resp->getBody());

        if (isset($json->message) && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (isset($json->order) && 'ok' === $json->status) {
            /** @var \Nekofar\Nobitex\Client\OrderTrait $order */
            $order = $this->jsonMapper->map($json->order, new Order());

            return $order;
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
    public function setMarketOrderStatus(array $args): bool
    {
        if (!array_key_exists('order', $args) || in_array($args['order'], [null, '', 0], true)) {
            throw new InvalidArgumentException("Order id is invalid.");
        }

        if (!array_key_exists('status', $args) || in_array($args['status'], [null, ''], true)) {
            throw new InvalidArgumentException("Order status is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/market/orders/update-status', [], $data); // phpcs:ignore
        $json = json_decode((string) $resp->getBody());

        if (isset($json->message) && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        return isset($json->updatedStatus) && 'ok' === $json->status;
    }
}
