<?php

namespace Nekofar\Nobitex\Auth;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    public function testRefreshToken()
    {
        $username = 'username';
        $password = 'password';
        $remember = true;

        $accessToken = md5('accessToken');

        $httpClient = new Client();
        $httpClient->addResponse(new Response(200, [], json_encode(['key' => $accessToken])));

        $auth = new Basic($username, $password, $remember, $httpClient);
        $this->assertEquals($accessToken, $auth->refreshToken());
    }


    public function testAuthenticate()
    {

        $username = 'username';
        $password = 'password';
        $remember = true;

        $accessToken = md5('accessToken');

        $httpClient = new Client();
        $httpClient->addResponse(new Response(200, [], json_encode(['key' => $accessToken])));

        $auth = new Basic($username, $password, $remember, $httpClient);
        $auth->refreshToken();

        $request = new Request('GET', '/');

        $header = $auth->authenticate($request)->getHeaderLine('Authorization');
        $this->assertEquals(sprintf('Token %s', $accessToken), $header);
    }
}
