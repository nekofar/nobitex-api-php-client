<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Model;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 */
class Profile
{
    /**
     * @var string
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $username;

    /**
     * @var string
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $email;

    /**
     * @var integer
     *
     * @JMS\Expose()
     *
     * @JMS\Type("integer")
     */
    public $level;

    /**
     * @var string
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $firstName;

    /**
     * @var string
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $lastName;

    /**
     * @var string
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $nationalCode;

    /**
     * @var string
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $nickname;

    /**
     * @var string
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $phone;

    /**
     * @var string
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $mobile;

    /**
     * @var string|null
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $province;

    /**
     * @var string
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $city;

    /**
     * @var string
     *
     * @JMS\Expose()
     *
     * @JMS\Type("string")
     */
    public $address;

    /**
     * @var array<\Nekofar\Nobitex\Model\Card>
     *
     * @JMS\Expose()
     *
     * @JMS\Type("array<Nekofar\Nobitex\Model\Card>")
     *
     * @JMS\SerializedName("bankCards")
     */
    public $cards;

    /**
     * @var array<\Nekofar\Nobitex\Model\Account>
     *
     * @JMS\Expose()
     *
     * @JMS\Type("array<Nekofar\Nobitex\Model\Account>")
     *
     * @JMS\SerializedName("bankAccounts")
     */
    public $accounts;

    /**
     * @var array<boolean|integer|string>
     *
     * @JMS\Expose()
     *
     * @JMS\Type("array")
     */
    public $verifications;

    /**
     * @var array<boolean|integer|string>
     *
     * @JMS\Expose()
     *
     * @JMS\Type("array")
     */
    public $pendingVerifications;

    /**
     * @var array<boolean|integer|string>
     *
     * @JMS\Expose()
     *
     * @JMS\Type("array")
     */
    public $options;

    /**
     * @var boolean
     *
     * @JMS\Expose()
     *
     * @JMS\Type("boolean")
     */
    public $withdrawEligible;
}
