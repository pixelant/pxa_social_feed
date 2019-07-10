<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

/**
 * Class FacebookSource
 * @package Pixelant\PxaSocialFeed\Feed\Source
 */
class FacebookSource extends BaseSource
{
    /**
     * Load feed source
     *
     * @return array Feed items
     */
    public function load(): array
    {
        $fb = $this->getConfiguration()->getToken()->getFb();
        $response = $fb->get($this->generateEndPoint());

        $body = $response->getDecodedBody();

        return is_array($body) ? ($body['data'] ?: []) : [];
    }

    /**
     * Generate facebook endpoint
     *
     * @return string
     */
    protected function generateEndPoint(): string
    {
        $limit = $this->getConfiguration()->getMaxItems();

        $fields = [
            'likes.summary(true).limit(0)',
            'message',
            'attachments',
            'created_time',
            'updated_time',
        ];

        list($fields) = $this->emitSignal('facebookEndPointRequestFields', [$fields]);

        $url = $this->getConfiguration()->getSocialId() . '/feed';
        $queryParams = [
            'fields' => implode(',', $fields),
            'limit' => $limit
        ];

        $endPoint = $url . '?' . http_build_query($queryParams);

        list($endPoint) = $this->emitSignal('faceBookEndPoint', [$endPoint]);

        return $endPoint;
    }
}
