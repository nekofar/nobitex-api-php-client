<?php


namespace Nekofar\Nobitex\Model;

class Order
{
    /**
     * @var string
     */
    // phpcs:ignore
    public $type;

    /**
     * @var string
     */
    // phpcs:ignore
    public $execution;

    /**
     * @var string
     */
    // phpcs:ignore
    public $srcCurrency;

    /**
     * @var string
     */
    // phpcs:ignore
    public $dstCurrency;

    /**
     * @var float
     */
    // phpcs:ignore
    public $price;

    /**
     * @var float
     */
    // phpcs:ignore
    public $amount;

    /**
     * @var float
     */
    // phpcs:ignore
    public $totalPrice;

    /**
     * @var string
     */
    // phpcs:ignore
    public $matchedAmount;


    /**
     * @var float
     */
    // phpcs:ignore
    public $unmatchedAmount;

    /**
     * @var bool
     */
    // phpcs:ignore
    public $isMyOrder;
}