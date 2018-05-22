<?php

namespace Pixelant\PxaSocialFeed\Utility\Api;

use Facebook\Facebook;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FacebookSDKUtility
{
    /**
     * @param Token $token
     * @param string $apiVersion
     * @return Facebook
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public static function getFacebook(Token $token, $apiVersion = '')
    {
        return new Facebook([
            'app_id' => $token->getCredential('appId'),
            'app_secret' => $token->getCredential('appSecret'),
            'default_graph_version' => $apiVersion ? $apiVersion : self::getApiVersion()
        ]);
    }

    /**
     * @return string
     */
    public static function getApiVersion()
    {
        $configurationUtility = GeneralUtility::makeInstance(ConfigurationUtility::class);
        $config = $configurationUtility->getConfiguration();
        return $config['settings']['facebookGraphVersion']
            ? $config['settings']['facebookGraphVersion']
            : 'v3.0';
    }
}
