<?php
/**
 * @package Nekofar\Nobitex
 * @author Milad Nekofar <milad@nekofar.com>
 */

namespace Nekofar\Nobitex\Model;

use JsonMapper;
use JsonMapper_Exception;

class Profile // phpcs:ignore
{
    /**
     * @var string
     */
    public $username; // phpcs:ignore
    /**
     * @var string
     */
    public $email; // phpcs:ignore
    /**
     * @var integer
     */
    public $level; // phpcs:ignore
    /**
     * @var string
     */
    public $firstName; // phpcs:ignore
    /**
     * @var string
     */
    public $lastName; // phpcs:ignore
    /**
     * @var string
     */
    public $nationalCode; // phpcs:ignore
    /**
     * @var string
     */
    public $nickname; // phpcs:ignore
    /**
     * @var string
     */
    public $phone; // phpcs:ignore
    /**
     * @var string
     */
    public $mobile; // phpcs:ignore
    /**
     * @var string|null
     */
    public $province; // phpcs:ignore
    /**
     * @var string
     */
    public $city; // phpcs:ignore
    /**
     * @var string
     */
    public $address; // phpcs:ignore
    /**
     * @var Card[]
     */
    public $cards; // phpcs:ignore
    /**
     * @var Account[]
     */
    public $accounts; // phpcs:ignore
    /**
     * @var array
     */
    public $verifications; // phpcs:ignore
    /**
     * @var array
     */
    public $pendingVerifications; // phpcs:ignore
    /**
     * @var array
     */
    public $options; // phpcs:ignore
    /**
     * @var boolean
     */
    public $withdrawEligible; // phpcs:ignore


    /**
     * @param $object
     * @param $propName
     * @param $jsonValue
     * @throws JsonMapper_Exception
     */
    public static function setUndefinedProperty($object, $propName, $jsonValue) // phpcs:ignore
    {
        $mapper = new JsonMapper();

        if ($propName === 'bankCards') {
            $object->{'cards'} = $mapper->mapArray($jsonValue, [], Card::class);
        }

        if ($propName === 'bankAccounts') {
            $object->{'accounts'} = $mapper->mapArray($jsonValue, [], Account::class);
        }
    }
}
