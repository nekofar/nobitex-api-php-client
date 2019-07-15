<?php


namespace Nekofar\Nobitex;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\HttpClient;
use JsonMapper;

class Client
{

    /**
     * @var HttpClient
     */
    private $http;

    /**
     * @var JsonMapper
     */
    private $mapper;


    /**
     * @param Config $config
     * @return Client
     */
    public static function create(Config $config)
    {
        return new static($config->createHttpClient(), $config->createJsonMapper());
    }


    /**
     * Client constructor.
     *
     * @param HttpMethodsClient $http
     * @param JsonMapper $mapper
     */
    public function __construct(HttpMethodsClient $http, JsonMapper $mapper)
    {
        $this->http = $http;
        $this->mapper = $mapper;
    }
}
