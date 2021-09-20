<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex;

use Http\Client\Common\HttpMethodsClient;
use JMS\Serializer\SerializerBuilder;
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
     * @var \JMS\Serializer\SerializerInterface
     */
    private $serializer;

    /**
     * Client constructor.
     */
    public function __construct(HttpMethodsClient $http, JsonMapper $mapper)
    {
        $this->httpClient = $http;
        $this->jsonMapper = $mapper;

        $this->serializer = SerializerBuilder::create()->build();
    }

    /**
     *
     */
    public static function create(Config $config): Client
    {
        return new self(
            $config->createHttpClient(),
            $config->createJsonMapper(),
        );
    }
}
