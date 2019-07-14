<?php
/**
 * @package Nekofar\Nobitex\Auth
 */

namespace Nekofar\Nobitex\Auth;

use Http\Client\Exception;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Message\Authentication;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;
use Nekofar\Nobitex\Config;
use Psr\Http\Message\RequestInterface;

/**
 * Class Basic
 */
class Basic implements Authentication
{

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $remember;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @var StreamFactory
     */
    private $streamFactory;


    /**
     * Basic constructor.
     *
     * @param string $username Username for authentication.
     * @param string $password Password for authentication.
     * @param bool $remember Long term token generation.
     * @param HttpClient $httpClient
     * @param RequestFactory $requestFactory
     * @param StreamFactory $streamFactory
     */
    public function __construct(
        $username,
        $password,
        $remember = true,
        HttpClient $httpClient = null,
        RequestFactory $requestFactory = null,
        StreamFactory $streamFactory = null
    ) {
        $this->apiUrl = Config::DEFAULT_API_URL;

        $this->username = $username;
        $this->password = $password;
        $this->remember = $remember;

        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
        $this->streamFactory = $streamFactory ?: StreamFactoryDiscovery::find();
    }

    /**
     * Refresh authentication access token.
     *
     * @return boolean|string
     * @throws Exception
     */
    public function refreshToken()
    {
        $response = $this->httpClient
            ->sendRequest(
                $this->requestFactory
                    ->createRequest(
                        'POST',
                        $this->apiUrl . '/auth/login/',
                        ['Content-Type' => 'application/json'],
                        json_encode([
                            'username' => $this->username,
                            'password' => $this->password,
                            'remember' => $this->remember === true ? 'yes' : 'no',
                        ])
                    )
            );

        $this->accessToken = $response->getStatusCode() === 200 ?
            json_decode($response->getBody())->key : null;

        return $this->accessToken;
    }

    /**
     * Authenticates a request.
     *
     * @param RequestInterface $request
     *
     * @return RequestInterface
     */
    public function authenticate(RequestInterface $request)
    {
        if ($this->accessToken !== null) {
            return $request->withHeader(
                'Authorization',
                sprintf('Token %s', $this->accessToken)
            );
        }

        return $request;
    }
}
