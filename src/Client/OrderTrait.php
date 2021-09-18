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
use Nekofar\Nobitex\Model\Order;

/**
 * Trait OrderTrait
 */
trait OrderTrait
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
     * @param array $args
     *
     * @return Order|false
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function addMarketOrder(array $args)
    {
        if (!isset($args['type']) ||
            empty($args['type'])) {
            throw new InvalidArgumentException("Order type is invalid.");
        }

        if (!isset($args['srcCurrency']) ||
            empty($args['srcCurrency'])) {
            throw new InvalidArgumentException("Source currency is invalid.");
        }

        if (!isset($args['dstCurrency']) ||
            empty($args['dstCurrency'])) {
            throw new InvalidArgumentException("Destination currency is invalid."); // phpcs:ignore
        }

        if (!isset($args['amount']) ||
            empty($args['amount'])) {
            throw new InvalidArgumentException("Order amount is invalid.");
        }

        if (!isset($args['price']) ||
            empty($args['price'])) {
            throw new InvalidArgumentException("Order price is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/market/orders/add', [], $data);
        $json = json_decode($resp->getBody());

        if (isset($json->message) && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (isset($json->order) && 'ok' === $json->status) {
            /** @var Order $order */
            $order = $this->jsonMapper->map($json->order, new Order());

            return $order;
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return OrderTrait|false
     *
     * @throws JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getMarketOrder(array $args)
    {
        if (!isset($args['id']) ||
            empty($args['id'])) {
            throw new InvalidArgumentException("Order id is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/market/orders/status', [], $data);
        $json = json_decode($resp->getBody());

        if (isset($json->message) && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (isset($json->order) && 'ok' === $json->status) {
            /** @var OrderTrait $order */
            $order = $this->jsonMapper->map($json->order, new Order());

            return $order;
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
    public function setMarketOrderStatus(array $args)
    {
        if (!isset($args['order']) ||
            empty($args['order'])) {
            throw new InvalidArgumentException("Order id is invalid.");
        }

        if (!isset($args['status']) ||
            empty($args['status'])) {
            throw new InvalidArgumentException("Order status is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/market/orders/update-status', [], $data); // phpcs:ignore
        $json = json_decode($resp->getBody());

        if (isset($json->message) && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (isset($json->updatedStatus) && 'ok' === $json->status) {
            return true;
        }

        return false;
    }
}
