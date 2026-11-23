<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Pixelant\PxaSocialFeed\Domain\Model\Token;
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
    public const GRAPH_VERSION = 'v24.0';

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
            'access_token'    => $this->getRequestToken()->getAccessToken(),
            'appsecret_proof' => hash_hmac(
                'sha256',
                $this->getRequestToken()->getAccessToken(),
                $this->getRequestToken()->getAppSecret()
            ),
        ];

        $endPoint = $this->addFieldsAsGetParametersToUrl($url, $queryParams);
        $event    = $eventDispatcher->dispatch(new FacebookEndPointEvent($endPoint));

        return $event->getEndPoint();
    }

    protected function getRequestToken(): Token
    {
        return $this->getConfiguration()->getToken();
    }

    /**
     * Get data from facebook
     *
     * @param array $response
     * @return array
     */
    protected function getDataFromResponse(mixed $response): array
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
