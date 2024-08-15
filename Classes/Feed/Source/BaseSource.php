<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Exception\BadResponseException;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class BaseSource
 */
abstract class BaseSource implements FeedSourceInterface
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Get configuration
     *
     * @return Configuration
     */
    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    /**
     * Append endpoint url with get parameters based on fields
     *
     * @param string $url
     * @param array $fields
     * @return string
     */
    protected function addFieldsAsGetParametersToUrl(string $url, array $fields): string
    {
        $url .= empty($fields) ? '' : ('?' . http_build_query($fields));

        return $url;
    }

    /**
     * Get request to api url
     *
     * @param string $url
     * @param array $additionalOptions
     * @return ResponseInterface
     * @throws BadResponseException
     */
    protected function performApiGetRequest(string $url, array $additionalOptions = []): ResponseInterface
    {
        /** @var RequestFactory $requestFactory */
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);

        /** @var ResponseInterface $response */
        $response = $requestFactory->request(
            $url,
            'GET',
            $additionalOptions
        );

        if ($response->getStatusCode() === 200) {
            return $response;
        }
        $body = (string)$response->getBody();
        // @codingStandardsIgnoreStart
        throw new BadResponseException("Api request return status '{$response->getStatusCode()}' while trying to request '$url' with message '$body'", 1562910160643);
        // @codingStandardsIgnoreEnd
    }
}
