<?php


namespace Nekofar\Nobitex;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\Authentication;
use JsonMapper;
use Nekofar\Nobitex\Auth\Basic;

/**
 * Class Config
 *
 * @package Nekofar\Nobitex
 */
class Config
{
    const DEFAULT_API_URL = 'https://api.nobitex.ir';

    /**
     * @var Basic
     */
    private $auth;

    /**
     * @var string
     */
    private $apiUrl;


    /**
     * Config constructor.
     * @param Authentication $auth
     */
    public function __construct(Authentication $auth)
    {
        $this->auth = new AuthenticationPlugin($auth);
        $this->apiUrl = self::DEFAULT_API_URL;
    }

    /**
     * @param  $username
     * @param  $password
     * @param  $remember
     * @return Config
     */
    public static function doAuth($username, $password, $remember)
    {
        return new static(new Basic($username, $password, $remember));
    }


    /**
     * @return JsonMapper
     */
    public function createJsonMapper()
    {
        return new JsonMapper();
    }


    /**
     * @return HttpMethodsClient
     */
    public function createHttpClient()
    {
        return new HttpMethodsClient(
            new PluginClient(HttpClientDiscovery::find(), [$this->auth]),
            MessageFactoryDiscovery::find()
        );
    }
}
