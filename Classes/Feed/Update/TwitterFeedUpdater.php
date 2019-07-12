<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Update;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Model\Feed;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Feed\Source\FeedSourceInterface;

/**
 * Class TwitterFeedUpdater
 * @package Pixelant\PxaSocialFeed\Feed\Update
 */
class TwitterFeedUpdater extends BaseUpdater
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
                $rawData['id_str'],
                $source->getConfiguration()->getStorage()
            );

            if ($feedItem === null) {
                $feedItem = $this->createFeedItem($rawData, $source->getConfiguration());
            }

            $this->updateFeedItem($feedItem, $rawData);
            
            // Call hook
            $this->emitSignal('beforeUpdateTwitterFeed', [$feedItem, $rawData, $source->getConfiguration()]);

            $this->feedRepository->{$feedItem->_isNew() ? 'add' : 'update'}($feedItem);
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
        $feedItem = $this->objectManager->get(Feed::class);
        $date = new \DateTime($rawData['created_at']);

        $feedItem->setPostDate($date);
        $feedItem->setPostUrl(
            'https://twitter.com/' . $configuration->getSocialId() . '/status/' . $rawData['id_str']
        );
        $feedItem->setConfiguration($configuration);
        $feedItem->setExternalIdentifier($rawData['id_str']);
        $feedItem->setPid($configuration->getStorage());
        $feedItem->setType(Token::TWITTER);

        return $feedItem;
    }

    /**
     * Update feed item properties with raw data
     *
     * @param Feed $feedItem
     * @param array $rawData
     * @return void
     */
    protected function updateFeedItem(Feed $feedItem, array $rawData): void
    {
        // Update text
        $text = $rawData['full_text'] ?: $rawData['text'] ?: '';
        if ($feedItem->getMessage() != $text) {
            $feedItem->setMessage($text);
        }

        // Media
        $image = $rawData['entities']['media'][0]['media_url'] ?? '';
        if ($feedItem->getImage() != $image) {
            $feedItem->setImage($image);
        }

        $likes = intval($rawData['retweeted_status']['favorite_count'] ?? $rawData['favorite_count'] ?? 0);

        if ($likes != $feedItem->getLikes()) {
            $feedItem->setLikes($likes);
        }
    }
}
