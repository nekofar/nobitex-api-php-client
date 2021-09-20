<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Model;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 */
class Card
{
    /**
     * @var string
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $number;

    /**
     * @var string
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $bank;

    /**
     * @var string
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $owner;

    /**
     * @var boolean
     *
     * @JMS\Expose()
     *
     * @JMS\Type("boolean")
     */
    public $confirmed;

    /**
     * @var string
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $status;
}
