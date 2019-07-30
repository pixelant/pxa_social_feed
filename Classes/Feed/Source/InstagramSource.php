<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Facebook\Facebook;

/**
 * Class InstagramSource
 * @package Pixelant\PxaSocialFeed\Feed\Source
 */
class InstagramSource extends BaseFacebookSource
{
    /**
     * Load feed source
     *
     * @return array Feed items
     */
    public function load(): array
    {
        $fb = $this->getConfiguration()->getToken()->getFb();

        $instagramId = $this->getInstagramId($fb);

        $response = $fb->get($this->generateEndPoint($instagramId, 'media'));

        return $this->getDataFromResponse($response);
    }

    /**
     * Fetch instagram ID
     *
     * @param Facebook $fb
     * @return string
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    protected function getInstagramId(Facebook $fb): string
    {
        $pageId = $this->getConfiguration()->getSocialId();

        $response = $fb->get($pageId . '?fields=instagram_business_account');
        $responseBody = $response->getDecodedBody();

        if (empty($responseBody['instagram_business_account']['id'])) {
            // @codingStandardsIgnoreStart
            throw new \UnexpectedValueException("Could not get instagram bussines account ID for page with ID '$pageId'. Check you settings.", 1562841411121);
            // @codingStandardsIgnoreEnd
        }

        return $responseBody['instagram_business_account']['id'];
    }

    /**
     * Return fields for endpoint request
     *
     * @return array
     */
    protected function getEndPointFields(): array
    {
        return [
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
        ];
    }
}
