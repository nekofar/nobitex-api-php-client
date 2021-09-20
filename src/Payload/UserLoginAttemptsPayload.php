<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Payload;

use JMS\Serializer\Annotation as JMS;

/**
 *
 */
class UserLoginAttemptsPayload implements PayloadInterface
{
    /**
     * @var string|null
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $status;

    /**
     * @var string|null
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $code;

    /**
     * @var string|null
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $message;

    /**
     * @var array<array<string,string>>|null
     *
     * @JMS\Expose()
     *
     * @JMS\Type("array")
     */
    public $attempts;

    /**
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return array<array<string,string>>|null
     */
    public function getAttempts(): ?array
    {
        return $this->attempts;
    }
}
