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
    public static function factory(Token $token, string $version = null): Facebook
    {
        $fb = GeneralUtility::makeInstance(
            Facebook::class,
            [
                'app_id' => $token->getAppId(),
                'app_secret' => $token->getAppSecret(),
                'default_access_token' => $token->getAccessToken(),
                'default_graph_version' => $version ?? static::$version,
            ]
        );

        return $fb;
    }
}
