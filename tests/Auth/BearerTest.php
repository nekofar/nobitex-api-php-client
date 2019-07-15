<?php

namespace Nekofar\Nobitex\Auth;

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class BearerTest extends TestCase
{

    public function testAuthenticate()
    {
        $accessToken = md5('accessToken');

        $auth = new Bearer($accessToken);

        $request = new Request('GET', '/');

        $header = $auth->authenticate($request)->getHeaderLine('Authorization');
        $this->assertEquals(sprintf('Token %s', $accessToken), $header);
    }
}
