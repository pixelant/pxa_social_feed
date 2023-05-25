<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Update;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Model\Feed;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Feed\Source\FeedSourceInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TwitterFeedUpdater
 */
class TwitterV2FeedUpdater extends BaseUpdater
{
    /**
     * Create/Update feed items
     *
     * @param FeedSourceInterface $source
     */
    public function update(FeedSourceInterface $source): void
    {
        $payload = $source->load();

        $items = $payload['data'] ?? [];
        $includes = $payload['includes'] ?? [];

        foreach ($items as $rawData) {
            $feedItem = $this->feedRepository->findOneByExternalIdentifier(
                $rawData['id'],
                $source->getConfiguration()->getStorage()
            );

            if ($feedItem === null) {
                $feedItem = $this->createFeedItem($rawData, $source->getConfiguration());
            }

            $this->updateFeedItem($feedItem, $rawData, $includes);

            // Call hook
            $this->emitSignal('beforeUpdateTwitterFeed', [$feedItem, $rawData, $source->getConfiguration(), $includes]);

            $this->addOrUpdateFeedItem($feedItem);
        }
    }

    /**
     * Create new twitter feed
     *
     * @param array $rawData
     * @param Configuration $configuration
     * @return Feed
     */
    protected function createFeedItem(array $rawData, Configuration $configuration): Feed
    {
        $feedItem = GeneralUtility::makeInstance(Feed::class);
        $date = new \DateTime($rawData['created_at']);

        $feedItem->setPostDate($date);
        $feedItem->setPostUrl(
            'https://twitter.com/' . $configuration->getSocialId() . '/status/' . $rawData['id']
        );
        $feedItem->setConfiguration($configuration);
        $feedItem->setExternalIdentifier($rawData['id']);
        $feedItem->setPid($configuration->getStorage());
        $feedItem->setType(Token::TWITTER_V2);

        return $feedItem;
    }

    /**
     * Update feed item properties with raw data
     *
     * @param Feed $feedItem
     * @param array $rawData
     * @param array $includes
     */
    protected function updateFeedItem(Feed $feedItem, array $rawData, array $includes): void
    {
        // Update text
        $text = $rawData['text'];
        if ($feedItem->getMessage() != $text) {
            $feedItem->setMessage($this->encodeMessage($text));
        }

        // Media
        $url = '';
        $mediaKey = $rawData['attachments']['media_keys'][0] ?? '';
        if ($mediaKey !== '') {
            foreach ($includes['media'] ?? [] as $media) {
                if ($media['media_key'] === $mediaKey) {
                    $url = $media['preview_image_url'] ?? $media['url'] ?? '';
                    break;
                }
            }
        }
        $imageRef = $this->storeImg($url, $feedItem);
        if ($imageRef != null && !$this->checkIfFalRelationIfAlreadyExists($feedItem->getFalMedia(), $imageRef)) {
            $feedItem->addFalMedia($imageRef);
        }

        $likes = (int)($rawData['public_metrics']['like_count'] ?? 0);
        if ($likes !== $feedItem->getLikes()) {
            $feedItem->setLikes($likes);
        }
    }
}
