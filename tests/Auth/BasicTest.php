<?php

/**
 * @package Nekofar\Nobitex
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

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
     * @throws \JsonException
     */
    public function testRefreshToken(): void
    {
        $username = 'username';
        $password = 'password';
        $remember = true;

        $accessToken = md5('accessToken');

        $httpClient = new Client();
        $httpClient->addResponse(new Response(200, [], json_encode(['key' => $accessToken], JSON_THROW_ON_ERROR)));

        $auth = new Basic($username, $password, $remember, null, $httpClient);
        self::assertEquals($accessToken, $auth->refreshToken());
    }

    /**
     * @throws Exception
     * @throws \JsonException
     */
    public function testAuthenticate(): void
    {

        $username = 'username';
        $password = 'password';
        $remember = true;

        $accessToken = md5('accessToken');

        $httpClient = new Client();
        $httpClient->addResponse(new Response(200, [], json_encode(['key' => $accessToken], JSON_THROW_ON_ERROR)));

        $auth = new Basic($username, $password, $remember, null, $httpClient);
        $auth->refreshToken();

        $request = new Request('GET', '/');

        $header = $auth->authenticate($request)->getHeaderLine('Authorization');
        self::assertEquals(sprintf('Token %s', $accessToken), $header);
    }

    /**
     * @throws Exception
     * @throws \JsonException
     */
    public function testAuthenticateWithToken(): void
    {

        $username = 'username';
        $password = 'password';
        $remember = true;

        $totpToken = 123456;

        $accessToken = md5('accessToken');

        $httpClient = new Client();
        $httpClient->addResponse(new Response(200, [], json_encode(['key' => $accessToken], JSON_THROW_ON_ERROR)));

        $auth = new Basic($username, $password, $remember, $totpToken, $httpClient);
        $auth->refreshToken();

        /** @var RequestInterface $request */
        $request = $httpClient->getLastRequest();

        $header = $request->getHeaderLine('X-TOTP');
        self::assertEquals($totpToken, $header);
    }

    /**
     * @throws Exception
     */
    public function testAuthenticateWithFailure(): void
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
        self::assertNotEquals(sprintf('Token %s', $accessToken), $header);
    }
}
