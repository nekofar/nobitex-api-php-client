<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Auth;

use Http\Message\Authentication;
use Psr\Http\Message\RequestInterface;

/**
 * Class Bearer
 */
class Bearer implements Authentication
{
    /**
     * @var string
     */
    private $token;

    /**
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Authenticates a request.
     */
    public function authenticate(RequestInterface $request): RequestInterface
    {
        $header = sprintf('Token %s', $this->token);

        return $request->withHeader('Authorization', $header);
    }
}
