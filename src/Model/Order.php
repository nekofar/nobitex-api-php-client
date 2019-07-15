<?php


namespace Nekofar\Nobitex\Model;

class Order
{
    /**
     * @var string
     */
    public $type; // phpcs:ignore
    /**
     * @var string
     */
    public $execution; // phpcs:ignore
    /**
     * @var string
     */
    public $srcCurrency; // phpcs:ignore
    /**
     * @var string
     */
    public $dstCurrency; // phpcs:ignore
    /**
     * @var float
     */
    public $price; // phpcs:ignore
    /**
     * @var float
     */
    public $amount; // phpcs:ignore
    /**
     * @var float
     */
    public $totalPrice; // phpcs:ignore
    /**
     * @var string
     */
    public $matchedAmount; // phpcs:ignore

    /**
     * @var float
     */
    public $unmatchedAmount; // phpcs:ignore
    /**
     * @var bool
     */
    public $isMyOrder; // phpcs:ignore
}
