<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Payload;

use JMS\Serializer\Annotation as JMS;
use Nekofar\Nobitex\Model\Profile;

/**
 * @JMS\ExclusionPolicy("all")
 */
class UserProfilePayload implements PayloadInterface
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
    public $message;

    /**
     * @var \Nekofar\Nobitex\Model\Profile|null
     *
     * @JMS\Expose()
     *
     * @JMS\Type("Nekofar\Nobitex\Model\Profile")
     */
    public $profile;

    /**
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     */
    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    /**
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }
}
