<?php
/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

namespace Nekofar\Nobitex;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\Authentication;
use JsonMapper;
use Nekofar\Nobitex\Auth\Basic;

/**
 * Class Config
 */
class Config
{
    const DEFAULT_API_URL = 'https://api.nobitex.ir';
    const TESTNET_API_URL = 'https://testnetapi.nobitex.ir';

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
        $this->auth = $auth;
        $this->apiUrl = self::DEFAULT_API_URL;
    }

    /**
     * @param string $username
     * @param string $password
     * @param boolean $remember
     * @param integer|null $totpToken
     *
     * @return Config
     */
    public static function doAuth(
        $username,
        $password,
        $remember = true,
        $totpToken = null
    ) {
        return new static(new Basic(
            $username,
            $password,
            $remember,
            $totpToken
        ));
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
                new AuthenticationPlugin($this->auth),
                new HeaderDefaultsPlugin([
                    'Content-Type' => 'application/json',
                ]),
                new BaseUriPlugin(
                    UriFactoryDiscovery::find()->createUri($this->apiUrl),
                    ['replace' => true]
                ),
            ]),
            MessageFactoryDiscovery::find()
        );
    }
}
