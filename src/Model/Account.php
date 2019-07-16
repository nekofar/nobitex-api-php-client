<?php
/**
 * @package Nekofar\Nobitex
 * @author Milad Nekofar <milad@nekofar.com>
 */

namespace Nekofar\Nobitex\Model;

class Account
{
    /**
     * @var integer
     */
    public $id; // phpcs:ignore
    /**
     * @var string
     */
    public $number; // phpcs:ignore
    /**
     * @var string
     */
    public $shaba; // phpcs:ignore
    /**
     * @var string
     */
    public $bank; // phpcs:ignore
    /**
     * @var string
     */
    public $owner; // phpcs:ignore
    /**
     * @var boolean
     */
    public $confirmed; // phpcs:ignore
    /**
     * @var string
     */
    public $status; // phpcs:ignore
}
