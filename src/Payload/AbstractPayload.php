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
abstract class AbstractPayload implements PayloadInterface
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
}
