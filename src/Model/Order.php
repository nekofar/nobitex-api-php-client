<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Model;

/**
 * Class Order
 */
class Order
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $execution;

    /**
     * @var string
     */
    public $srcCurrency;

    /**
     * @var string
     */
    public $dstCurrency;

    /**
     * @var float
     */
    public $price;

    /**
     * @var float
     */
    public $amount;

    /**
     * @var float
     */
    public $totalPrice;

    /**
     * @var float
     */
    public $matchedAmount;

    /**
     * @var float
     */
    public $unmatchedAmount;

    /**
     * @var boolean
     */
    public $isMyOrder;

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $status;

    /**
     * @var boolean
     */
    public $partial;

    /**
     * @var float
     */
    public $fee;

    /**
     * @var string
     */
    public $user;

    /**
     * @var \DateTime
     */
    public $createdAt;
}
