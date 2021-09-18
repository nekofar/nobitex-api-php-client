<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Model;

/**
 * Class Withdraw
 */
class Withdraw
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $blockchainUrl;

    /**
     * @var boolean
     */
    public $isCancelable;

    /**
     * @var string
     */
    public $status;

    /**
     * @var float
     */
    public $amount;

    /**
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @var integer
     */
    public $walletId;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $address;

    /**
     * @param mixed $jsonValue
     *
     * @throws \Exception
     */
    public static function setUndefinedProperty(object $object, string $propName, $jsonValue): void
    {
        if ('blockchain_url' === $propName) {
            $object->{'blockchainUrl'} = $jsonValue;
        }

        if ('is_cancelable' === $propName) {
            $object->{'isCancelable'} = $jsonValue;
        }

        if ('wallet_id' === $propName) {
            $object->{'walletId'} = $jsonValue;
        }
    }
}
