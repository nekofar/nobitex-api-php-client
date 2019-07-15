<?php


namespace Nekofar\Nobitex;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Exception;
use Http\Client\HttpClient;
use JsonMapper;
use JsonMapper_Exception;
use Nekofar\Nobitex\Model\Order;
use Nekofar\Nobitex\Model\Profile;
use Nekofar\Nobitex\Model\Trade;

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
            [],
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

    /**
     * @param array $params
     * @return Trade[]
     * @throws Exception
     * @throws JsonMapper_Exception
     */
    public function getMarketTrades($params = [])
    {
        $trades = [];
        $params = $params + ['srcCurrency' => 'btc', 'dstCurrency' => 'rls'];

        $response = $this->http->post(
            Config::DEFAULT_API_URL . '/market/trades/list',
            [],
            json_encode($params)
        );

        if ($response->getStatusCode() === 200) {
            $json = json_decode($response->getBody());
            if (isset($json->trades)) {
                $trades = $this->mapper->mapArray($json->trades, [], 'Nekofar\Nobitex\Model\Trade');
            }
        }

        return $trades;
    }

    /**
     * @return Profile
     * @throws Exception
     * @throws JsonMapper_Exception
     */
    public function getUserProfile()
    {
        $profile = new Profile();

        $response = $this->http->post(
            Config::DEFAULT_API_URL . '/users/profile'
        );

        if ($response->getStatusCode() === 200) {
            $json = json_decode($response->getBody());
            if (isset($json->profile)) {
                $this->mapper->undefinedPropertyHandler = [Profile::class, 'setUndefinedProperty'];
                $profile = $this->mapper->map($json->profile, $profile);
            }
        }

        return $profile;
    }
}
