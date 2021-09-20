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
class UserProfilePayload extends AbstractPayload
{
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
    public function getProfile(): ?Profile
    {
        return $this->profile;
    }
}
