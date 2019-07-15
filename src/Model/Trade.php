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
    public $srcCurrency; // phpcs:ignore
    /**
     * @var string
     */
    public $dstCurrency; // phpcs:ignore
    /**
     * @var \DateTime
     */
    public $timestamp; // phpcs:ignore
    /**
     * @var string
     */
    public $market; // phpcs:ignore
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
    public $total; // phpcs:ignore
    /**
     * @var string
     */
    public $type; // phpcs:ignore
    /**
     * @var float
     */
    public $fee; // phpcs:ignore
}
