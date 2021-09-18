<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Model;

/**
 * Class Wallet
 */
class Wallet
{
    /**
     * @var float
     */
    public $activeBalance;
    /**
     * @var float
     */
    public $blockedBalance;
    /**
     * @var string
     */
    public $user;
    /**
     * @var string
     */
    public $currency;
    /**
     * @var integer
     */
    public $id;
    /**
     * @var float
     */
    public $balance;
    /**
     * @var float
     */
    public $rialBalance;
    /**
     * @var float
     */
    public $rialBalanceSell;
    /**
     * @var string|null
     */
    public $depositAddress;
}
