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
class UserLoginAttemptsPayload extends AbstractPayload
{
    /**
     * @var array<array<string,string>>|null
     *
     * @JMS\Expose()
     *
     * @JMS\Type("array")
     */
    public $attempts;

    /**
     * @return array<array<string,string>>|null
     */
    public function getAttempts(): ?array
    {
        return $this->attempts;
    }
}
