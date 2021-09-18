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
use Nekofar\Nobitex\Model\Trade;

/**
 * Trait Trade
 */
trait TradeTrait
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
     * @param array $args
     *
     * @return \Nekofar\Nobitex\Model\Trade[]|false Return and array on success or false on
 * unexpected errors.
     *
     * @throws \JsonMapper_Exception
     * @throws \Http\Client\Exception
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function getMarketTrades(array $args)
    {
        if (!array_key_exists('srcCurrency', $args) || in_array($args['srcCurrency'], [null, ''], true)) {
            throw new InvalidArgumentException("Source currency is invalid.");
        }

        if (!array_key_exists('dstCurrency', $args) || in_array($args['dstCurrency'], [null, ''], true)) {
            throw new InvalidArgumentException("Destination currency is invalid."); // phpcs:ignore
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/market/trades/list', [], $data);
        $json = json_decode((string)$resp->getBody());

        if (isset($json->message) && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (isset($json->trades) && 'ok' === $json->status) {
            return $this->jsonMapper->mapArray($json->trades, [], Trade::class);
        }

        return false;
    }
}
