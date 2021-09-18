<?php

/**
 * @package Nekofar\Nobitex
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Auth;

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class BearerTest extends TestCase
{

    public function testAuthenticate(): void
    {
        $accessToken = md5('accessToken');

        $auth = new Bearer($accessToken);

        $request = new Request('GET', '/');

        $header = $auth->authenticate($request)->getHeaderLine('Authorization');
        self::assertEquals(sprintf('Token %s', $accessToken), $header);
    }
}
