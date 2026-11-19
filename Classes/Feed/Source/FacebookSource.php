<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Domain\Repository\TokenRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FacebookSource
 */
class FacebookSource extends BaseFacebookSource
{
    private ?Token $pageToken = null;

    /**
     * Load feed source
     *
     * @return array Feed items
     */
    public function load(): array
    {
        $configuredToken = $this->getConfiguration()->getToken();
        $pageAccessToken = $configuredToken->isFacebookPageType() ? $configuredToken
            : GeneralUtility::makeInstance(TokenRepository::class)->findFacebookPageToken(
                $configuredToken,
                $this->configuration->getSocialId()
            )->getFirst();

        if ($pageAccessToken === null) {
            throw new \UnexpectedValueException('Generate a Facebook page token for the configured page before importing.', 1731600002);
        }
        $this->pageToken = $pageAccessToken;
        $endPointEntry = $this->getConfiguration()->getEndPointEntry();
        if (!in_array($endPointEntry, ['feed', 'posts'])) {
            $endPointEntry = 'feed';
        }
        $endPointUrl = $this->generateEndPoint($this->getConfiguration()->getSocialId(), $endPointEntry);
        $response = $this->performApiGetRequest('https://graph.facebook.com/' . self::GRAPH_VERSION . '/' . $endPointUrl);
        $response = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $this->getDataFromResponse($response);
    }

    protected function getRequestToken(): Token
    {
        return $this->pageToken ?? parent::getRequestToken();
    }

    /**
     * Return fields for endpoint request
     *
     * @return array
     */
    protected function getEndPointFields(): array
    {
        return [
            'reactions.summary(true).limit(0)',
            'message',
            'attachments',
            'permalink_url',
            'created_time',
            'updated_time',
        ];
    }
}
