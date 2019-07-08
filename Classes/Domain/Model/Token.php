<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Pixelant\PxaSocialFeed\Utility\Api\FacebookSDKUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Tokens
 */
class Token extends AbstractEntity
{

    /**
     * facebook token
     */
    const FACEBOOK = 1;

    /**
     * instagram_oauth2
     */
    const INSTAGRAM = 2;

    /**
     * twitter token
     */
    const TWITTER = 3;

    /**
     * youtube token
     */
    const YOUTUBE = 4;

    /**
     * type
     *
     * @var integer
     */
    protected $type = 0;

    /**
     * oAuthTypes
     *
     * @var array
     */
    protected $oAuthSocialTypes = [self::FACEBOOK, self::INSTAGRAM];

    /**
     * @var string
     */
    protected $appId = '';

    /**
     * @var string
     */
    protected $appSecret = '';

    /**
     * @var string
     */
    protected $accessToken = '';

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     */
    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    /**
     * @return string
     */
    public function getAppSecret(): string
    {
        return $this->appSecret;
    }

    /**
     * @param string $appSecret
     */
    public function setAppSecret(string $appSecret): void
    {
        $this->appSecret = $appSecret;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * get value for select box
     * @return string
     */
    public function getTitle(): string
    {
        return LocalizationUtility::translate('module.type.' . $this->getType(), 'PxaSocialFeed') ?? '';
    }

    /**
     * isOAuthToken
     *
     * @return bool
     */
    public function getIsOAuthToken()
    {
        return in_array($this->getSocialType(), $this->oAuthSocialTypes);
    }

    /**
     * @param $returnUri
     * @return string
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function getTokenGenerationUri($returnUri)
    {
        switch ($this->getSocialType()) {
            case self::INSTAGRAM_OAUTH2:
                $uri = 'https://api.instagram.com/oauth/authorize/';
                $uri .= '?client_id=' . $this->getCredential('clientId');
                $uri .= '&redirect_uri=' . urlencode($returnUri);
                $uri .= '&response_type=code&scope=public_content';
                return $uri;
            case self::FACEBOOK_OAUTH2:
                $fb = FacebookSDKUtility::getFacebook($this);

                $helper = $fb->getRedirectLoginHelper();

                //TODO: make configurable
                $permissions = ['manage_pages', 'instagram_basic', 'instagram_manage_insights'];

                return $helper->getLoginUrl($returnUri, $permissions);
        }
    }

    /**
     * Check if is facebook token type
     *
     * @return bool
     */
    public function isFacebookType(): bool
    {
        return $this->type === static::FACEBOOK;
    }

    /**
     * Return all available types
     *
     * @return array
     */
    public static function getAvailableTokensTypes(): array
    {
        return [
            static::FACEBOOK,
            static::INSTAGRAM,
            static::TWITTER,
            static::YOUTUBE,
        ];
    }
}
