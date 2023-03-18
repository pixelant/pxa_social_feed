<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Provider;

use League\OAuth2\Client\Provider\AppSecretProof;
use League\OAuth2\Client\Token\AccessToken;
use Pixelant\PxaSocialFeed\Domain\Model\FacebookPage;
use Pixelant\PxaSocialFeed\Domain\Model\FacebookUser;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @method FacebookUser getResourceOwner(AccessToken $token)
 */
class Facebook extends \League\OAuth2\Client\Provider\Facebook
{
    /**
     * A toggle to enable the beta tier URL's.
     */
    protected bool $enableBetaMode = false;

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @return FacebookPage[]
     */
    public function getLongLivePageTokens(string $userId, AccessToken $token): array
    {
        $response = $this->fetchLongLivePageTokens($userId, $token);
        if (!isset($response['data']) || !is_array($response['data'])) {
            return [];
        }

        return array_map(fn ($page) => GeneralUtility::makeInstance(FacebookPage::class, $page), $response['data']);
    }

    /**
     * @param AccessToken $token The Facebook User token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        $fields = [
            'id', 'name', 'first_name', 'last_name', 'accounts',
            'email', 'hometown', 'picture.type(large){url,is_silhouette}',
            'gender', 'age_range',
        ];

        // backwards compatibility less than 2.8
        if (version_compare(substr($this->graphApiVersion, 1), '2.8') < 0) {
            $fields[] = 'bio';
        }

        $appSecretProof = AppSecretProof::create($this->clientSecret, $token->getToken());

        return $this->getBaseGraphUrl().$this->graphApiVersion.'/me?fields='.implode(',', $fields)
                        .'&access_token='.$token.'&appsecret_proof='.$appSecretProof;
    }

    /**
     * @param array<string, mixed> $response
     */
    protected function createResourceOwner(array $response, AccessToken $token): FacebookUser
    {
        return new FacebookUser($response);
    }

    /**
     * @return array<string, mixed>
     */
    protected function fetchLongLivePageTokens(string $userId, AccessToken $token): array
    {
        $request = $this->getAuthenticatedRequest(
            self::METHOD_GET,
            $this->getLongLivePageTokenUrl($userId, $token),
            $token
        );

        $response = $this->getParsedResponse($request);

        if (false === is_array($response)) {
            throw new \UnexpectedValueException(
                'Invalid response received from Authorization Server. Expected JSON.'
            );
        }

        return $response;
    }

    /**
     * @return string
     */
    protected function getBaseGraphUrl(): string
    {
        return $this->enableBetaMode ? static::BASE_GRAPH_URL_BETA : static::BASE_GRAPH_URL;
    }

    /**
     * @param string      $userId
     * @param AccessToken $token
     *
     * @return string
     */
    protected function getLongLivePageTokenUrl(string $userId, AccessToken $token): string
    {
        $appSecretProof = AppSecretProof::create($this->clientSecret, $token->getToken());

        return $this->getBaseGraphUrl().$this->graphApiVersion.
               "/{$userId}/accounts?access_token=".$token.'&appsecret_proof='.$appSecretProof;
    }
}
