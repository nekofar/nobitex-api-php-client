<?php


namespace Nekofar\Nobitex;

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
    public function create(Config $config)
    {
        return new static($config->createHttpClient(), $config->createJsonMapper());
    }


    /**
     * Client constructor.
     *
     * @param HttpClient $http
     * @param JsonMapper $mapper
     */
    public function __construct(HttpClient $http, JsonMapper $mapper)
    {
        $this->http = $http;
        $this->mapper = $mapper;
    }
}
