<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Client;

use Exception;
use InvalidArgumentException;
use Nekofar\Nobitex\Model\Profile;

/**
 * Trait User
 */
trait UserTrait
{

    /**
     * @var \Http\Client\Common\HttpMethodsClient
     */
    private $httpClient;

    /**
     * @var \JsonMapper
     */
    private $jsonMapper;

    /**
     * @return \Nekofar\Nobitex\Model\Profile|false Return a Profile object on success or false on
 * unexpected errors
     *
     * @throws \Http\Client\Exception
     * @throws \JsonMapper_Exception
     * @throws \Exception
     */
    public function getUserProfile()
    {
        $resp = $this->httpClient->post('/users/profile');
        $json = json_decode((string)$resp->getBody());

        if (property_exists($json, 'message') && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (property_exists($json, 'profile') && 'ok' === $json->status) {
            $this->jsonMapper->undefinedPropertyHandler = [
                Profile::class,
                'setUndefinedProperty',
            ];

            /** @var \Nekofar\Nobitex\Model\Profile $profile */
            $profile = $this->jsonMapper->map($json->profile, new Profile());

            return $profile;
        }

        return false;
    }

    /**
     * @return array|false Return an array on success or false on
     *                     unexpected errors.
     *
     * @throws \Http\Client\Exception
     * @throws \Exception
     */
    public function getUserLoginAttempts()
    {
        $resp = $this->httpClient->post('/users/login-attempts');
        $json = json_decode((string)$resp->getBody());

        if (property_exists($json, 'message') && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (property_exists($json, 'attempts') && 'ok' === $json->status) {
            return (array)$json->attempts;
        }

        return false;
    }

    /**
     * @return string|false
     *
     * @throws \Http\Client\Exception
     * @throws \Exception
     */
    public function getUserReferralCode()
    {
        $resp = $this->httpClient->post('/users/get-referral-code');
        $json = json_decode((string)$resp->getBody());

        if (property_exists($json, 'message') && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (property_exists($json, 'referralCode') && 'ok' === $json->status) {
            return $json->referralCode;
        }

        return false;
    }

    /**
     * @param array $args
     *
     *
     * @throws \Http\Client\Exception
     * @throws \Exception
     */
    public function addUserCard(array $args): bool
    {
        if (!array_key_exists('bank', $args) || in_array($args['bank'], [null, ''], true)) {
            throw new InvalidArgumentException("Bank name is invalid.");
        }

        if (!array_key_exists('number', $args) || !preg_match('/^[0-9]{16}$/', $args['number'])) {
            throw new InvalidArgumentException("Card number is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/cards-add', [], $data);
        $json = json_decode((string)$resp->getBody());

        if (property_exists($json, 'message') && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        return property_exists($json, 'status') && 'ok' === $json->status;
    }

    /**
     * @param array $args
     *
     *
     * @throws \Http\Client\Exception
     * @throws \Exception
     */
    public function addUserAccount(array $args): bool
    {
        if (!array_key_exists('bank', $args) || in_array($args['bank'], [null, ''], true)) {
            throw new InvalidArgumentException("Bank name is invalid.");
        }

        if (!array_key_exists('number', $args) || !preg_match('/^[0-9]+$/', $args['number'])) {
            throw new InvalidArgumentException("Account number is invalid.");
        }

        if (!array_key_exists('shaba', $args) || !preg_match('/^IR[0-9]{24}$/', $args['shaba'])) {
            throw new InvalidArgumentException("Account shaba is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/account-add', [], $data);
        $json = json_decode((string)$resp->getBody());

        if (property_exists($json, 'message') && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        return property_exists($json, 'status') && 'ok' === $json->status;
    }

    /**
     * @return array|false Return an array on success or false on
     *                     unexpected errors.
     *
     * @throws \Http\Client\Exception
     * @throws \Exception
     */
    public function getUserLimitations()
    {
        $resp = $this->httpClient->post('/users/get-referral-code');
        $json = json_decode((string)$resp->getBody());

        if (property_exists($json, 'message') && 'failed' === $json->status) {
            throw new Exception($json->message);
        }

        if (property_exists($json, 'limitations') && 'ok' === $json->status) {
            return json_decode(json_encode($json->limitations), true);
        }

        return false;
    }
}
