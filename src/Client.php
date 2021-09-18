<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex;

use Http\Client\Common\HttpMethodsClient;
use JsonMapper;

/**
 * Class Client
 */
class Client
{
    use Client\StatTrait;
    use Client\OrderTrait;
    use Client\TradeTrait;
    use Client\UserTrait;
    use Client\WalletTrait;

    /**
     * @var \Http\Client\HttpClient
     */
    private $httpClient;

    /**
     * @var \JsonMapper
     */
    private $jsonMapper;

    /**
     * Client constructor.
     *
     * @param \Http\Client\Common\HttpMethodsClient $http
     * @param \JsonMapper $mapper
     */
    public function __construct(HttpMethodsClient $http, JsonMapper $mapper)
    {
        $this->httpClient = $http;
        $this->jsonMapper = $mapper;
    }

    /**
     * @param \Nekofar\Nobitex\Config $config
     *
     */
    public static function create(Config $config): Client
    {
        return new static(
            $config->createHttpClient(),
            $config->createJsonMapper()
        );
    }
}
