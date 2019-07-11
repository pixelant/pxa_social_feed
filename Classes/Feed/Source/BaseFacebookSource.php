<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Facebook\FacebookResponse;
use Pixelant\PxaSocialFeed\Exception\InvalidFeedSourceData;

/**
 * Class BaseFacebookSource
 * @package Pixelant\PxaSocialFeed\Feed\Source
 */
abstract class BaseFacebookSource extends BaseSource
{
    /**
     * Generate facebook endpoint
     *
     * @param string $id
     * @param string $endPointEntry
     * @return string
     */
    protected function generateEndPoint(string $id, string $endPointEntry): string
    {
        $limit = $this->getConfiguration()->getMaxItems();

        $fields = $this->getEndPointFields();

        list($fields) = $this->emitSignal('facebookEndPointRequestFields', [$fields]);

        $url = $id . '/' . $endPointEntry;
        $queryParams = [
            'fields' => implode(',', $fields),
            'limit' => $limit
        ];

        $endPoint = $url . '?' . http_build_query($queryParams);

        list($endPoint) = $this->emitSignal('faceBookEndPoint', [$endPoint]);

        return $endPoint;
    }

    /**
     * Get data from facebook
     *
     * @param FacebookResponse $response
     * @return array
     */
    protected function getDataFromResponse(FacebookResponse $response): array
    {
        $body = $response->getDecodedBody();

        if (!is_array($body) || !isset($body['data'])) {
            throw new InvalidFeedSourceData("Invalid data received for configuration {$this->getConfiguration()->getName()}.", 1562842385128);
        }
        
        return $body['data'];
    }

    /**
     * Return fields for endpoint request
     *
     * @return array
     */
    abstract protected function getEndPointFields(): array;
}
