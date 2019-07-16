<?php
/**
 * @package Nekofar\Nobitex
 * @author Milad Nekofar <milad@nekofar.com>
 */

namespace Nekofar\Nobitex\Auth;

use Http\Message\Authentication;
use Psr\Http\Message\RequestInterface;

class Bearer implements Authentication
{

    /**
     * @var string
     */
    private $token;

    /**
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
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
        $header = sprintf('Token %s', $this->token);

        return $request->withHeader('Authorization', $header);
    }
}
