<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Pixelant\PxaSocialFeed\SignalSlot\EmitSignalTrait;
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

        $items = $this->requestTwitterApi(
            $this->addFieldsAsGetParametersToUrl($endPointUrl, $fields),
            $authHeader
        );
    }

    protected function requestTwitterApi(string $url, string $autHeader): array
    {
        $additionalOptions = [
            'headers' => [
                'Authorization' => $autHeader
            ]
        ];

        /** @var RequestFactory $requestFactory */
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);

        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $requestFactory->request(
            $url,
            'GET',
            $additionalOptions
        );

        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(json_decode($response->getBody(), true),$response->getStatusCode(),16);
        die;
        if ($response->getStatusCode() === 200) {
            return $response->getBody();
        } else {
            throw new ServerCommunicationException(
                BaseController::translate('pxasocialfeed_module.labels.errorCommunication'),
                1478084292
            );
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
     * Get API url, for easy extend
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

        // Important to pass field value as string, because it encoded with rawurlencode
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
