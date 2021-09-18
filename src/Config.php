<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\ErrorPlugin;
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
     * @var \Nekofar\Nobitex\Auth\Basic
     */
    private $auth;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * Config constructor.
     */
    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
        $this->apiUrl = self::DEFAULT_API_URL;
    }

    /**
     *
     */
    public static function doAuth(
        string $username,
        string $password,
        bool $remember = true,
        ?int $totpToken = null
    ): Config {
        return new static(new Basic(
            $username,
            $password,
            $remember,
            $totpToken
        ));
    }


    /**
     */
    public function createJsonMapper(): JsonMapper
    {
        return new JsonMapper();
    }


    /**
     */
    public function createHttpClient(): HttpMethodsClient
    {
        return new HttpMethodsClient(
            new PluginClient(HttpClientDiscovery::find(), [
                new ErrorPlugin(),
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
