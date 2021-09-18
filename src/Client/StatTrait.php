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

/**
 * Trait Stat
 */
trait StatTrait
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
     * @return array|false Return an array on success or false on
     *                     unexpected errors.
     *
     * @throws \Http\Client\Exception
     * @throws \Exception
     */
    public function getMarketStats(array $args)
    {
        if (!array_key_exists('srcCurrency', $args) || in_array($args['srcCurrency'], [null, ''], true)) {
            throw new InvalidArgumentException("Source currency is invalid.");
        }

        if (!array_key_exists('dstCurrency', $args) || in_array($args['dstCurrency'], [null, ''], true)) {
            throw new InvalidArgumentException("Destination currency is invalid."); // phpcs:ignore
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/market/stats', [], $data);
        $json = json_decode((string)$resp->getBody());

        if (property_exists($json, 'message') && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (property_exists($json, 'stats') && 'ok' === $json->status) {
            return (array)$json->stats->{"{$args['srcCurrency']}-{$args['dstCurrency']}"};
        }

        return false;
    }
}
