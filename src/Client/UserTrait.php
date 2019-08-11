<?php
/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

namespace Nekofar\Nobitex\Client;

use Exception;
use Http\Client\Common\HttpMethodsClient;
use InvalidArgumentException;
use JsonMapper;
use JsonMapper_Exception;
use Nekofar\Nobitex\Model\Profile;

/**
 * Trait User
 */
trait UserTrait
{

    /**
     * @var HttpMethodsClient
     */
    private $httpClient;

    /**
     * @var JsonMapper
     */
    private $jsonMapper;

    /**
     * @return Profile|false Return a Profile object on success or false on
     *                       unexpected errors
     *
     * @throws \Http\Client\Exception
     * @throws JsonMapper_Exception
     * @throws Exception
     */
    public function getUserProfile()
    {
        $resp = $this->httpClient->post('/users/profile');
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->profile) && $json->status === 'ok') {
            $this->jsonMapper->undefinedPropertyHandler = [
                Profile::class,
                'setUndefinedProperty',
            ];

            /** @var Profile $profile */
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
     * @throws Exception
     */
    public function getUserLoginAttempts()
    {
        $resp = $this->httpClient->post('/users/login-attempts');
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->attempts) && $json->status === 'ok') {
            return (array)$json->attempts;
        }

        return false;
    }

    /**
     * @return string|false
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getUserReferralCode()
    {
        $resp = $this->httpClient->post('/users/get-referral-code');
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->referralCode) && $json->status === 'ok') {
            return $json->referralCode;
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return bool
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function addUserCard(array $args)
    {
        if (!isset($args['bank']) ||
            empty($args['bank'])) {
            throw new InvalidArgumentException("Bank name is invalid.");
        }

        if (!isset($args['number']) ||
            !preg_match('/^[0-9]{16}$/', $args['number'])) {
            throw new InvalidArgumentException("Card number is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/cards-add', [], $data);
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->status) && $json->status === 'ok') {
            return true;
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return bool
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function addUserAccount(array $args)
    {
        if (!isset($args['bank']) ||
            empty($args['bank'])) {
            throw new InvalidArgumentException("Bank name is invalid.");
        }

        if (!isset($args['number']) ||
            !preg_match('/^[0-9]+$/', $args['number'])) {
            throw new InvalidArgumentException("Account number is invalid.");
        }

        if (!isset($args['shaba']) ||
            !preg_match('/^IR[0-9]{24}$/', $args['shaba'])) {
            throw new InvalidArgumentException("Account shaba is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/account-add', [], $data);
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->status) && $json->status === 'ok') {
            return true;
        }

        return false;
    }

    /**
     * @return array|false Return an array on success or false on
     *                     unexpected errors.
     *
     * @throws \Http\Client\Exception
     * @throws Exception
     */
    public function getUserLimitations()
    {
        $resp = $this->httpClient->post('/users/get-referral-code');
        $json = json_decode($resp->getBody());

        if (isset($json->message) && $json->status === 'failed') {
            throw new Exception($json->message);
        }

        if (isset($json->limitations) && $json->status === 'ok') {
            return json_decode(json_encode($json->limitations), true);
        }

        return false;
    }
}
