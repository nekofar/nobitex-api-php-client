<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Payload;

/**
 * @link https://apidocs.nobitex.ir/#d9a7503222
 *
 * @property string|null $status
 * @property string|null $code
 * @property string|null $message
 */
interface PayloadInterface
{
    /**
     */
    public function getStatus(): ?string;

    /**
     */
    public function getCode(): ?string;

    /**
     */
    public function getMessage(): ?string;
}
