<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Payload;

/**
 *
 */
interface PayloadInterface
{
    /**
     */
    public function getStatus(): ?string;
}
