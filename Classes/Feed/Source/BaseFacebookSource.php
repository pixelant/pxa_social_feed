<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Pixelant\PxaSocialFeed\Exception\InvalidFeedSourceData;

/**
 * Class BaseFacebookSource
 * @package Pixelant\PxaSocialFeed\Feed\Source
 */
abstract class BaseFacebookSource extends BaseSource
{
    const GRAPH_VERSION = 'v12.0';

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
            'limit' => $limit,
            'access_token' => $this->getConfiguration()->getToken()->getAccessToken(),
            'appsecret_proof' => hash_hmac(
                'sha256',
                $this->getConfiguration()->getToken()->getAccessToken(),
                $this->getConfiguration()->getToken()->getAppSecret()
            ),
        ];

        $endPoint = $this->addFieldsAsGetParametersToUrl($url, $queryParams);

        list($endPoint) = $this->emitSignal('faceBookEndPoint', [$endPoint]);

        return $endPoint;
    }

    /**
     * Get data from facebook
     *
     * @param array $response
     * @return array
     */
    protected function getDataFromResponse(array $response): array
    {
        if (!is_array($response) || !isset($response['data'])) {
            throw new InvalidFeedSourceData(
                'Invalid data received for configuration ' . $this->getConfiguration()->getName() . '.',
                1562842385128
            );
        }

        return $response['data'];
    }

    /**
     * Return fields for endpoint request
     *
     * @return array
     */
    abstract protected function getEndPointFields(): array;
}
