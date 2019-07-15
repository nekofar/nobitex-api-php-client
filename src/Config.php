<?php


namespace Nekofar\Nobitex;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\Authentication;
use JsonMapper;
use Nekofar\Nobitex\Auth\Basic;
use spec\Http\Client\Common\Plugin\HeaderDefaultsPluginSpec;

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
     * @param string $username
     * @param string $password
     * @param bool $remember
     * @param int|null $totpToken
     * @return Config
     */
    public static function doAuth($username, $password, $remember = true, $totpToken = null)
    {
        return new static(new Basic($username, $password, $remember, $totpToken));
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
            new PluginClient(HttpClientDiscovery::find(), [
                $this->auth,
                new HeaderDefaultsPlugin([
                    'Content-Type' => 'application/json'
                ])
            ]),
            MessageFactoryDiscovery::find()
        );
    }
}
