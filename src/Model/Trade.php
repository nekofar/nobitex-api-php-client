<?php
/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

namespace Nekofar\Nobitex\Model;

/**
 * Class Trade
 */
class Trade
{
    /**
     * @var string
     */
    public $srcCurrency;
    /**
     * @var string
     */
    public $dstCurrency;
    /**
     * @var \DateTime
     */
    public $timestamp;
    /**
     * @var string
     */
    public $market;
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
    public $total;
    /**
     * @var string
     */
    public $type;
    /**
     * @var float
     */
    public $fee;
}
