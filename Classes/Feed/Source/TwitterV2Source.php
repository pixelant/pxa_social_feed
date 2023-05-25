<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Pixelant\PxaSocialFeed\Exception\BadResponseException;
use Pixelant\PxaSocialFeed\Exception\InvalidFeedSourceData;
use Psr\Http\Message\ResponseInterface;

/**
 * Class TwitterSource
 */
class TwitterV2Source extends BaseSource
{
    /**
     * Twitter api
     */
    const API_URL = 'https://api.twitter.com/2/';

    /**
     * Load feed source
     *
     * @return array Feed items
     */
    public function load(): array
    {
        $endPointUrl = $this->generateEndPointUrl(
            'users/:id/tweets',
            [':id' => $this->configuration->getSocialId()]
        );
        $fields = $this->getFields();

        $authHeader = $this->getAuthHeader();

        $response = $this->requestTwitterApi(
            $this->addFieldsAsGetParametersToUrl($endPointUrl, $fields),
            $authHeader
        );

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        if (!is_array($data)) {
            throw new InvalidFeedSourceData(
                "Twitter v2 response doesn't appear to be a valid json. Response return '$body'.",
                1684850941
            );
        }

        return $data;
    }

    /**
     * Request twitter api
     *
     * @param string $url
     * @param string $autHeader
     * @return ResponseInterface
     * @throws BadResponseException
     */
    protected function requestTwitterApi(string $url, string $autHeader): ResponseInterface
    {
        $additionalOptions = [
            'headers' => [
                'Authorization' => $autHeader,
            ],
        ];

        return $this->performApiGetRequest($url, $additionalOptions);
    }

    /**
     * Generate url for request
     *
     * @param string $endPoint
     * @param array $pathVariables
     * @return string
     */
    protected function generateEndPointUrl(string $endPoint, array $pathVariables = [])
    {
        $url = $this->getApiUrl() . $endPoint;

        foreach ($pathVariables as $key => $value) {
            $url = str_replace($key, rawurlencode($value), $url);
        }

        return $url;
    }

    /**
     * Get API url
     *
     * @return string
     */
    protected function getApiUrl(): string
    {
        return self::API_URL;
    }

    /**
     * Query fields
     *
     * @return array
     */
    protected function getFields(): array
    {
        $configuration = $this->getConfiguration();

        // Important to pass field value as string, because it's encoded with rawurlencode
        $fields = [
            'max_results' => (string)$configuration->getMaxItems(),
            'expansions' => 'attachments.media_keys,author_id',
            'tweet.fields' => 'created_at,public_metrics',
            'media.fields' => 'url,preview_image_url',
            'user.fields' => 'profile_image_url',
        ];

        [$fields] = $this->emitSignal('beforeReturnTwitterQueryFields', [$fields]);

        return $fields;
    }

    /**
     * Get Authorization header
     *
     * @return string
     */
    protected function getAuthHeader(): string
    {
        $token = $this->getConfiguration()->getToken()->getBearerToken();

        return 'Bearer ' . $token;
    }
}
