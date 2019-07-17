<?php
/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

namespace Nekofar\Nobitex\Model;

/**
 * Class Account
 */
class Account
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $number;
    /**
     * @var string
     */
    public $shaba;
    /**
     * @var string
     */
    public $bank;
    /**
     * @var string
     */
    public $owner;
    /**
     * @var bool
     */
    public $confirmed;
    /**
     * @var string
     */
    public $status;
}
