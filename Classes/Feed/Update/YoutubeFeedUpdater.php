<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Update;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Model\Feed;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Feed\Source\FeedSourceInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class YoutubeFeedUpdater
 */
class YoutubeFeedUpdater extends BaseUpdater
{
    /**
     * Create/Update feed items
     *
     * @param FeedSourceInterface $source
     */
    public function update(FeedSourceInterface $source): void
    {
        $items = $source->load();

        foreach ($items as $rawData) {
            $feedItem = $this->feedRepository->findOneByExternalIdentifier(
                $rawData['id']['videoId'],
                $source->getConfiguration()->getStorage()
            );

            if ($feedItem === null) {
                $feedItem = $this->createFeedItem($rawData, $source->getConfiguration());
            }

            $this->updateFeedItem($feedItem, $rawData);

            // Call hook
            $this->emitSignal('beforeUpdateYoutubeFeed', [$feedItem, $rawData, $source->getConfiguration()]);

            $this->addOrUpdateFeedItem($feedItem);
        }
    }

    /**
     * Update youtube feed item
     *
     * @param Feed $feedItem
     * @param array $rawData
     */
    protected function updateFeedItem(Feed $feedItem, array $rawData): void
    {
        $description = $rawData['snippet']['description'] ?? '';
        if ($description != $feedItem->getMessage()) {
            $feedItem->setMessage($this->encodeMessage($description));
        }

        $image = $rawData['snippet']['thumbnails']['high']['url'] ?? '';
        if ($image != $feedItem->getImage()) {
            $feedItem->setImage($image);
        }

        $title = $rawData['snippet']['title'] ?? '';
        if ($this != $feedItem->getTitle()) {
            $feedItem->setTitle($title);
        }
    }

    /**
     * Create new feed item
     *
     * @param array $rawData
     * @param Configuration $configuration
     * @return Feed
     */
    protected function createFeedItem(array $rawData, Configuration $configuration): Feed
    {
        $feedItem = GeneralUtility::makeInstance(Feed::class);

        $feedItem->setExternalIdentifier($rawData['id']['videoId']);
        $feedItem->setPostDate(new \DateTime($rawData['snippet']['publishedAt']));
        $feedItem->setPostUrl(
            sprintf(
                'https://www.youtube.com/watch?v=%s',
                $feedItem->getExternalIdentifier()
            )
        );
        $feedItem->setConfiguration($configuration);
        $feedItem->setType(Token::YOUTUBE);
        $feedItem->setPid($configuration->getStorage());

        return $feedItem;
    }
}
