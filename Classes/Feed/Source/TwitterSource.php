<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Pixelant\PxaSocialFeed\Exception\BadResponseException;
use Pixelant\PxaSocialFeed\Exception\InvalidFeedSourceData;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TwitterSource
 * @package Pixelant\PxaSocialFeed\Feed\Source
 */
class TwitterSource extends BaseSource
{
    /**
     * Twitter api
     */
    const API_URL = 'https://api.twitter.com/1.1/';

    /**
     * Load feed source
     *
     * @return array Feed items
     */
    public function load(): array
    {
        $endPointUrl = $this->generateEndPointUrl('statuses/user_timeline.json');
        $fields = $this->getFields();

        $authHeader = $this->getAuthHeader($endPointUrl, $fields);

        $response = $this->requestTwitterApi(
            $this->addFieldsAsGetParametersToUrl($endPointUrl, $fields),
            $authHeader
        );

        $body = (string)$response->getBody();
        $data = json_decode($body, true);

        if (!is_array($data)) {
            throw new InvalidFeedSourceData("Twitter response doesn't appear to be a valid json. '$body' returned by response.", 1562910457024);
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
                'Authorization' => $autHeader
            ]
        ];

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
        } else {
            $body = (string)$response->getBody();
            throw new BadResponseException("Twitter api return status '{$response->getStatusCode()}' while trying to request '$url' with message '$body'", 1562910160643);
        }
    }

    /**
     * Generate url for request
     *
     * @param string $endPoint
     * @return string
     */
    protected function generateEndPointUrl(string $endPoint)
    {
        return $this->getApiUrl() . $endPoint;
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
            'screen_name' => $configuration->getSocialId(),
            'count' => (string)$configuration->getMaxItems(),
            'tweet_mode' => 'extended',
            'exclude_replies' => '1',
            'include_rts' => '1'
        ];

        list($fields) = $this->emitSignal('beforeReturnTwitterQueryFields', [$fields]);

        return $fields;
    }

    /**
     * Get Authorization header
     *
     * @param string $url
     * @param array $fields
     * @return string
     */
    protected function getAuthHeader(string $url, array $fields): string
    {
        $token = $this->getConfiguration()->getToken();
        $oauth = [
            'oauth_consumer_key' => $token->getApiKey(),
            'oauth_nonce' => md5((string)mt_rand()),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $token->getAccessToken(),
            'oauth_timestamp' => (string)time(),
            'oauth_version' => '1.0'
        ];

        $sigBase = $this->buildSigBase(array_merge($oauth, $fields), $url);
        $sigKey = rawurlencode($token->getApiSecretKey()) . '&' . rawurlencode($token->getAccessTokenSecret());

        $oauth['oauth_signature'] = base64_encode(hash_hmac('sha1', $sigBase, $sigKey, true));

        $header = 'OAuth ';
        $headerValues = [];

        foreach ($oauth as $key => $value) {
            $headerValues[] = $key . '="' . rawurlencode($value) . '"';
        }

        $header .= implode(', ', $headerValues);

        return $header;
    }

    /**
     * Generate the base string
     *
     * @param array $oauth
     * @param string $url
     * @return string Built base string
     */
    protected function buildSigBase(array $oauth, string $url)
    {
        ksort($oauth);
        $urlParts = [];

        foreach ($oauth as $key => $value) {
            $urlParts[] = $key . '=' . rawurlencode($value);
        }

        return 'GET' . '&' . rawurlencode($url) . '&' . rawurlencode(implode('&', $urlParts));
    }
}