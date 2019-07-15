<?php


namespace Nekofar\Nobitex;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Exception;
use Http\Client\HttpClient;
use JsonMapper;
use JsonMapper_Exception;
use Nekofar\Nobitex\Model\Order;

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

    /**
     * @param array $params
     * @return Order[]
     * @throws Exception
     * @throws JsonMapper_Exception
     */
    public function getMarketOrders($params = [])
    {
        $orders = [];
        $params = $params + ['order' => 'price', 'type' => null, 'srcCurrency' => null, 'dstCurrency' => null];

        $response = $this->http->post(
            Config::DEFAULT_API_URL . '/market/orders/list',
            null,
            json_encode($params)
        );

        if ($response->getStatusCode() === 200) {
            $json = json_decode($response->getBody());
            if (isset($json->orders)) {
                $orders = $this->mapper->mapArray($json->orders, [], 'Nekofar\Nobitex\Model\Order');
            }
        }

        return $orders;
    }
}
