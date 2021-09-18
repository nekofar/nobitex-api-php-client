<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Model;

use JsonMapper;
use JsonMapper_Exception;

/**
 * Class Profile
 */
class Profile
{
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $email;
    /**
     * @var int
     */
    public $level;
    /**
     * @var string
     */
    public $firstName;
    /**
     * @var string
     */
    public $lastName;
    /**
     * @var string
     */
    public $nationalCode;
    /**
     * @var string
     */
    public $nickname;
    /**
     * @var string
     */
    public $phone;
    /**
     * @var string
     */
    public $mobile;
    /**
     * @var string|null
     */
    public $province;
    /**
     * @var string
     */
    public $city;
    /**
     * @var string
     */
    public $address;
    /**
     * @var Card[]
     */
    public $cards;
    /**
     * @var Account[]
     */
    public $accounts;
    /**
     * @var array
     */
    public $verifications;
    /**
     * @var array
     */
    public $pendingVerifications;
    /**
     * @var array
     */
    public $options;
    /**
     * @var bool
     */
    public $withdrawEligible;


    /**
     * @param object $object
     * @param string $propName
     * @param mixed $jsonValue
     *
     * @throws JsonMapper_Exception
     */
    public static function setUndefinedProperty($object, $propName, $jsonValue)
    {
        $mapper = new JsonMapper();

        if ('bankCards' === $propName) {
            $object->{'cards'} = $mapper->mapArray(
                $jsonValue,
                [],
                Card::class
            );
        }

        if ('bankAccounts' === $propName) {
            $object->{'accounts'} = $mapper->mapArray(
                $jsonValue,
                [],
                Account::class
            );
        }
    }
}
