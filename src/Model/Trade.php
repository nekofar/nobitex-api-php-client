<?php


namespace Nekofar\Nobitex\Model;

/**
 * Class Trade
 * @package Nekofar\Nobitex\Model
 */
class Trade
{

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
     * @var \DateTime
     */
    // phpcs:ignore
    public $timestamp;

    /**
     * @var string
     */
    // phpcs:ignore
    public $market;

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
    public $total;

    /**
     * @var string
     */
    // phpcs:ignore
    public $type;

    /**
     * @var float
     */
    // phpcs:ignore
    public $fee;
}
