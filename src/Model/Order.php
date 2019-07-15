<?php


namespace Nekofar\Nobitex\Model;

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
     * @var string
     */
    public $matchedAmount;


    /**
     * @var float
     */
    public $unmatchedAmount;

    /**
     * @var bool
     */
    public $isMyOrder;
}
