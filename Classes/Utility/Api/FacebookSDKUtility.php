<?php

namespace Pixelant\PxaSocialFeed\Utility\Api;

use Facebook\Facebook;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FacebookSDKUtility
{
    /**
     * @var null
     */
    protected $fb = null;

    /**
     * @var null
     */
    protected $accessToken = null;

    /**
     * FacebookSDKUtility constructor.
     * @param Token $token
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function __construct(Token $token)
    {
        $this->accessToken = $token->getCredential('accessToken');
        $this->fb = self::getFacebook($token);
    }
    
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

    /**
     * @param $endpoint
     * @return \Facebook\FacebookResponse
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    protected function get($endpoint)
    {
        return $this->fb->get($endpoint, $this->accessToken);
    }

    /**
     * @param string $facebookPageId
     * @return string $instagramAppAccountId
     * @throws \Exception
     */
    public function getInstagramIdFromFacebookPageId($facebookPageId)
    {
        $response = $this->get(
            $facebookPageId . '?fields=instagram_business_account'
        );

        $instagramAppAccountId = $response->getDecodedBody()['instagram_business_account']['id'];

        if (!$instagramAppAccountId) {
            throw new \Exception(
                'Can\'t find Instagram business account id. Please check the configuration.'
            );
        }

        return $instagramAppAccountId;
    }

    /**
     * @param $instagramAccountId
     * @param int $limit
     * @param array $fieldsList
     * @return array
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function getInstagramFeed($instagramAccountId, $limit = 5, $fieldsList = [])
    {
        $limit = intval($limit);

        // Set media endpoint
        $mediaEndpoint = 'media';
        $mediaEndpoint = $limit ? $mediaEndpoint . '.limit(' . $limit . ')' : $mediaEndpoint;

        // Set fields list
        $fieldsList = empty($fieldsList)
            ? [
                'caption',
                'children',
                'comments',
                'comments_count',
                'id',
                'ig_id',
                'is_comment_enabled',
                'like_count',
                'media_type',
                'media_url',
                'owner',
                'permalink',
                'shortcode',
                'thumbnail_url',
                'timestamp',
                'username'
            ]
            : $fieldsList;

        // Add fields list to media endpoint
        $mediaEndpoint = $fieldsList
            ? $mediaEndpoint . '{' . implode(',', $fieldsList) . '}'
            : $mediaEndpoint;

        // Make full endpoint
        $endpoint = '?fields=' . $mediaEndpoint;

        // Get response
        $response = $this->get(
            $instagramAccountId . '/' . $endpoint
        );

        // Get media from response
        return $response->getDecodedBody()['media']
            ? $response->getDecodedBody()['media']
            : [];
    }
}
