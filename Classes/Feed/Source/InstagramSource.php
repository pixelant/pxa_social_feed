<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

/**
 * Class InstagramSource
 */
class InstagramSource extends BaseFacebookSource
{
    public const BASE_INSTAGRAM_GRAPH_URL = 'https://graph.facebook.com/';

    /**
     * Load feed source
     *
     * @return array Feed items
     */
    public function load(): array
    {
        $instagramId = $this->getInstagramId();
        $endPointUrl = $this->generateEndPoint($instagramId, 'media');
        $response = file_get_contents(
            self::BASE_INSTAGRAM_GRAPH_URL .
            self::GRAPH_VERSION . '/' . $endPointUrl
        );
        $response = json_decode($response, true);

        return $this->getDataFromResponse($response);
    }

    /**
     * Fetch instagram ID
     *
     * @return string
     */
    protected function getInstagramId(): string
    {
        try {
            $pageId = $this->getConfiguration()->getSocialId();
            $access_token = $this->getConfiguration()->getToken()->getAccessToken();
            $response = file_get_contents(
                self::BASE_INSTAGRAM_GRAPH_URL . self::GRAPH_VERSION .
                '/' . $pageId . '?fields=instagram_business_account' .
                '&access_token=' . $access_token
            );
            $responseBody = json_decode($response, true);
        } catch (\Exception $exception) {
            throw new \UnexpectedValueException(
                'Could not get instagram business account ID for page with ID ' . $pageId . '. Check you settings.',
                1562841411121
            );
        }

        if (empty($responseBody['instagram_business_account']['id'])) {
            throw new \UnexpectedValueException(
                'Could not get instagram business account ID for page with ID ' . $pageId . '. Check you settings.',
                1562841411121
            );
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
            'username',
        ];
    }
}
