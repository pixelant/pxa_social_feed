<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Domain\Repository\TokenRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class FacebookSource
 * @package Pixelant\PxaSocialFeed\Feed\Source
 */
class FacebookSource extends BaseFacebookSource
{
    /**
     * Load feed source
     *
     * @return array Feed items
     */
    public function load(): array
    {
        // Get facebook page access token
        $tokenRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(TokenRepository::class);
        /** @var Token $pageAccessToken */
        $pageAccessToken = $tokenRepository->findFacebookPageToken(
            $this->getConfiguration()->getToken(),
            $this->configuration->getSocialId()
        )->getFirst();

        $fb = $pageAccessToken->getFb();
        $endPointUrl = $this->generateEndPoint($this->getConfiguration()->getSocialId(), 'feed');
        $response = file_get_contents(
            $fb::BASE_GRAPH_URL .
            self::GRAPH_VERSION . '/' . $endPointUrl
        );
        $response = json_decode($response, true);

        return $this->getDataFromResponse($response);
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
