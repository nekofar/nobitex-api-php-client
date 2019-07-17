<?php
/**
 * @package Nekofar\Nobitex
 * @author Milad Nekofar <milad@nekofar.com>
 */

namespace Nekofar\Nobitex\Auth;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Exception;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class BasicTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testRefreshToken()
    {
        $username = 'username';
        $password = 'password';
        $remember = true;

        $accessToken = md5('accessToken');

        $httpClient = new Client();
        $httpClient->addResponse(new Response(200, [], json_encode(['key' => $accessToken])));

        $auth = new Basic($username, $password, $remember, null, $httpClient);
        $this->assertEquals($accessToken, $auth->refreshToken());
    }

    /**
     * @throws Exception
     */
    public function testAuthenticate()
    {

        $username = 'username';
        $password = 'password';
        $remember = true;

        $accessToken = md5('accessToken');

        $httpClient = new Client();
        $httpClient->addResponse(new Response(200, [], json_encode(['key' => $accessToken])));

        $auth = new Basic($username, $password, $remember, null, $httpClient);
        $auth->refreshToken();

        $request = new Request('GET', '/');

        $header = $auth->authenticate($request)->getHeaderLine('Authorization');
        $this->assertEquals(sprintf('Token %s', $accessToken), $header);
    }

    /**
     * @throws Exception
     */
    public function testAuthenticateWithToken()
    {

        $username = 'username';
        $password = 'password';
        $remember = true;

        $totpToken = 123456;

        $accessToken = md5('accessToken');

        $httpClient = new Client();
        $httpClient->addResponse(new Response(200, [], json_encode(['key' => $accessToken])));

        $auth = new Basic($username, $password, $remember, $totpToken, $httpClient);
        $auth->refreshToken();

        /** @var RequestInterface $request */
        $request = $httpClient->getLastRequest();

        $header = $request->getHeaderLine('X-TOTP');
        $this->assertEquals($totpToken, $header);
    }

    /**
     * @throws Exception
     */
    public function testAuthenticateWithFailure()
    {

        $username = 'username';
        $password = 'password';
        $remember = true;

        $accessToken = md5('accessToken');

        $httpClient = new Client();
        $httpClient->addResponse(new Response(401));

        $auth = new Basic($username, $password, $remember, null, $httpClient);
        $auth->refreshToken();

        $request = new Request('GET', '/');

        $header = $auth->authenticate($request)->getHeaderLine('Authorization');
        $this->assertNotEquals(sprintf('Token %s', $accessToken), $header);
    }
}
