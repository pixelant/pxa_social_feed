<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Pixelant\PxaSocialFeed\Event\FacebookEndPointEvent;
use Pixelant\PxaSocialFeed\Event\FacebookEndPointRequestFieldsEvent;
use Pixelant\PxaSocialFeed\Exception\InvalidFeedSourceData;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class BaseFacebookSource
 */
abstract class BaseFacebookSource extends BaseSource
{
    public const GRAPH_VERSION = 'v12.0';

    /**
     * Generate facebook endpoint
     *
     * @param string $id
     * @param string $endPointEntry
     * @return string
     */
    protected function generateEndPoint(string $id, string $endPointEntry): string
    {
        $eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);
        $limit           = $this->getConfiguration()->getMaxItems();
        $fields          = $this->getEndPointFields();
        $event           = $eventDispatcher->dispatch(new FacebookEndPointRequestFieldsEvent($fields));
        $fieldsArray     = $event->getFields();
        $url = $id . '/' . $endPointEntry;

        $queryParams = [
            'fields'          => implode(',', $fieldsArray),
            'limit'           => $limit,
            'access_token'    => $this->getConfiguration()->getToken()->getAccessToken(),
            'appsecret_proof' => hash_hmac(
                'sha256',
                $this->getConfiguration()->getToken()->getAccessToken(),
                $this->getConfiguration()->getToken()->getAppSecret()
            ),
        ];

        $endPoint = $this->addFieldsAsGetParametersToUrl($url, $queryParams);
        $event    = $eventDispatcher->dispatch(new FacebookEndPointEvent($endPoint));

        // dd ( $event->getEndPoint () );
        // $endPointArray = $event->getEndPoint ();

        // list( $endPoint ) = $endPointArray;

        return $event->getEndPoint();
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
