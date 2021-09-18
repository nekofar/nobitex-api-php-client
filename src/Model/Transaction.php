<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Model;

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
     * @param object $object
     * @param string $propName
     * @param mixed $jsonValue
     *
     * @throws \Exception
     */
    public static function setUndefinedProperty($object, $propName, $jsonValue)
    {
        if ('created_at' === $propName) {
            $object->{'createdAt'} = new \DateTime($jsonValue);
        }
    }
}
