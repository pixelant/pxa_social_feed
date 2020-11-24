<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Domain\Repository\TokenRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        $tokenRepository = GeneralUtility::makeInstance(TokenRepository::class);
        /** @var Token $pageAccessToken */
        $pageAccessToken = $tokenRepository->findFacebookPageToken(
            $this->getConfiguration()->getToken(),
            $this->configuration->getSocialId()
        )->getFirst();

        $fb = $pageAccessToken->getFb();
        $response = $fb->get(
            $this->generateEndPoint($this->getConfiguration()->getSocialId(), 'feed')
        );

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
            'likes.summary(true).limit(0)',
            'message',
            'attachments',
            'created_time',
            'updated_time',
        ];
    }
}
