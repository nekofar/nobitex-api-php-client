<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Model;

use DateTime;

/**
 * Class Transaction
 */
class Transaction
{
    /**
     * @var string
     */
    public $currency;

    /**
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @var float
     */
    public $calculatedFee;

    /**
     * @var integer
     */
    public $id;

    /**
     * @var float
     */
    public $amount;

    /**
     * @var string
     */
    public $description;

    /**
     * @param mixed $jsonValue
     *
     * @throws \Exception
     */
    public static function setUndefinedProperty(object $object, string $propName, $jsonValue): void
    {
        if ('created_at' !== $propName) {
            return;
        }

        $object->{'createdAt'} = new DateTime($jsonValue);
    }
}
