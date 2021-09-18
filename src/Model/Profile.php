<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Model;

use JsonMapper;

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
     * @var array<\Nekofar\Nobitex\Model\Card>
     */
    public $cards;

    /**
     * @var array<\Nekofar\Nobitex\Model\Account>
     */
    public $accounts;

    /**
     * @codingStandardsIgnoreStart
     *
     * @var array
     */
    public $verifications; // @phpstan-ignore-line

    /**
     * @var array
     */
    public $pendingVerifications; // @phpstan-ignore-line

    /**
     * @var array
     */
    public $options; // @phpstan-ignore-line

    /**
     * @var boolean
     *
     * @codingStandardsIgnoreEnd
     */
    public $withdrawEligible;

    /**
     * @param mixed $jsonValue
     *
     * @throws \JsonMapper_Exception
     */
    public static function setUndefinedProperty(object $object, string $propName, $jsonValue): void
    {
        if (!in_array($propName, ['bankAccounts', 'bankCards'], true)) {
            return;
        }

        $mapper = new JsonMapper();

        if ('bankCards' === $propName) {
            // @phpstan-ignore-next-line
            $object->{'cards'} = $mapper->mapArray($jsonValue, [], Card::class);
        }

        // @phpstan-ignore-next-line
        $object->{'accounts'} = $mapper->mapArray($jsonValue, [], Account::class);
    }
}
