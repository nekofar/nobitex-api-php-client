<?php

/**
 * @package Nekofar\Nobitex
 *
 * @author Milad Nekofar <milad@nekofar.com>
 */

declare(strict_types=1);

namespace Nekofar\Nobitex\Client;

use InvalidArgumentException;
use Nekofar\Nobitex\Model\Profile;
use Nekofar\Nobitex\Payload\PayloadException;
use Nekofar\Nobitex\Payload\UserLoginAttemptsPayload;
use Nekofar\Nobitex\Payload\UserProfilePayload;

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
     * Return a Profile object on success or false on unexpected errors
     *
     * @throws \Http\Client\Exception
     * @throws \JsonMapper_Exception
     * @throws \Exception
     */
    public function getUserProfile(): ?Profile
    {
        $response = $this->httpClient->post('/users/profile');

        $payload = $this->serializer->deserialize(
            (string) $response->getBody(),
            UserProfilePayload::class,
            'json',
        );

        if ('ok' === $payload->getStatus()) {
            return $payload->getProfile();
        }

        throw new PayloadException($payload->getMessage() ?? '');
    }

    /**
     * Return an array on success or false on unexpected errors.
     *
     * @return array<array<string,string>>|null
     *
     * @throws \Http\Client\Exception
     * @throws \Exception
     */
    public function getUserLoginAttempts(): ?array
    {
        $response = $this->httpClient->post('/users/login-attempts');

        $payload = $this->serializer->deserialize(
            (string) $response->getBody(),
            UserLoginAttemptsPayload::class,
            'json',
        );

        if ('ok' === $payload->getStatus()) {
            return $payload->getAttempts();
        }

        throw new PayloadException($payload->getMessage() ?? '');
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
        $json = json_decode((string) $resp->getBody());

        if (isset($json->message) && 'failed' === $json->status) {
            throw new PayloadException($json->message);
        }

        if (isset($json->referralCode) && 'ok' === $json->status) {
            return $json->referralCode;
        }

        return false;
    }

    /**
     * @param array<string, integer|string> $args
     *
     * @throws \Http\Client\Exception
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function addUserCard(array $args): bool
    {
        if (
            !array_key_exists('bank', $args) ||
            in_array($args['bank'], [null, ''], true)
        ) {
            throw new InvalidArgumentException("Bank name is invalid.");
        }

        if (
            !array_key_exists('number', $args) ||
            1 !== preg_match('/^\d{16}$/', (string) $args['number'])
        ) {
            throw new InvalidArgumentException("Card number is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/cards-add', [], $data);
        $json = json_decode((string) $resp->getBody());

        if (isset($json->message) && 'failed' === $json->status) {
            throw new PayloadException($json->message);
        }

        return isset($json->status) && 'ok' === $json->status;
    }

    /**
     * @param array<string, integer|string> $args
     *
     * @throws \Http\Client\Exception
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function addUserAccount(array $args): bool
    {
        if (
            !array_key_exists('bank', $args) ||
            in_array($args['bank'], [null, ''], true)
        ) {
            throw new InvalidArgumentException("Bank name is invalid.");
        }

        if (
            !array_key_exists('number', $args) ||
            1 !== preg_match('/^\d+$/', (string) $args['number'])
        ) {
            throw new InvalidArgumentException("Account number is invalid.");
        }

        if (
            !array_key_exists('shaba', $args) ||
            1 !== preg_match('/^IR\d{24}$/', (string) $args['shaba'])
        ) {
            throw new InvalidArgumentException("Account shaba is invalid.");
        }

        $data = json_encode($args);
        $resp = $this->httpClient->post('/users/account-add', [], $data);
        $json = json_decode((string) $resp->getBody());

        if (isset($json->message) && 'failed' === $json->status) {
            throw new PayloadException($json->message);
        }

        return isset($json->status) && 'ok' === $json->status;
    }

    /**
     * Return an array on success or false on unexpected errors.
     *
     * @return array<string,string|array>|false
     *
     * @throws \Http\Client\Exception
     * @throws \Exception
     */
    public function getUserLimitations()
    {
        $resp = $this->httpClient->post('/users/get-referral-code');
        $json = json_decode((string) $resp->getBody());

        if (isset($json->message) && 'failed' === $json->status) {
            throw new PayloadException($json->message);
        }

        if (isset($json->limitations) && 'ok' === $json->status) {
            return json_decode(
                json_encode($json->limitations, JSON_THROW_ON_ERROR),
                true,
                512,
                JSON_THROW_ON_ERROR,
            );
        }

        return false;
    }
}
