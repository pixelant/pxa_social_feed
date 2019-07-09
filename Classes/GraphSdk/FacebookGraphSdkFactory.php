<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\GraphSdk;

use Facebook\Facebook;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FacebookGraphSdk
 * @package Pixelant\PxaSocialFeed\GraphSdk
 */
class FacebookGraphSdkFactory
{
    /**
     * Graph api version
     *
     * @var string
     */
    protected static $version = 'v3.3';

    /**
     * Facebook factory
     *
     * @param Token $token
     * @param string|null $version
     * @return Facebook
     */
    public static function getFbByToken(Token $token, string $version = null): Facebook
    {
        $fb = static::getFbByAppIdAndSecret(
            $token->getAppId(),
            $token->getAppSecret(),
            $token->getAccessToken(),
            $version
        );

        return $fb;
    }

    /**
     * Get fb instance
     * @param string $appId
     * @param string $appSecret
     * @param string|null $accessToken
     * @param string|null $version
     * @return Facebook
     */
    public static function getFbByAppIdAndSecret(
        string $appId,
        string $appSecret,
        string $accessToken = null,
        string $version = null
    ): Facebook {
        $arguments = [
            'app_id' => $appId,
            'app_secret' => $appSecret,
            'default_graph_version' => $version ?? static::$version,
        ];
        if (!empty($accessToken)) {
            $arguments['default_access_token'] = $accessToken;
        }

        return GeneralUtility::makeInstance(
            Facebook::class,
            $arguments
        );
    }
}
