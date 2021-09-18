<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Model;

/**
 * Class Deposit
 */
class Deposit
{
    /**
     * @var string
     */
    public $txHash;
    /**
     * @var string
     */
    public $address;
    /**
     * @var boolean
     */
    public $confirmed;
    /**
     * @var Transaction
     */
    public $transaction;
    /**
     * @var string
     */
    public $currency;
    /**
     * @var string
     */
    public $blockchainUrl;
    /**
     * @var integer
     */
    public $confirmations;
    /**
     * @var integer
     */
    public $requiredConfirmations;
    /**
     * @var float
     */
    public $amount;
}
