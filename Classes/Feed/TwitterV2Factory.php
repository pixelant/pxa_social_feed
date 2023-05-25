<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\Source\TwitterV2Source;
use Pixelant\PxaSocialFeed\Feed\Update\TwitterV2FeedUpdater;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TwitterV2Factory
 */
class TwitterV2Factory implements FeedFactoryInterface
{
    /**
     * Feed source allow to fetch feed items
     *
     * @param Configuration $configuration
     * @return TwitterV2Source
     */
    public function getFeedSource(Configuration $configuration): TwitterV2Source
    {
        return GeneralUtility::makeInstance(TwitterV2Source::class, $configuration);
    }

    /**
     * Feed updater. Will create/update feed items
     *
     * @return TwitterV2FeedUpdater
     */
    public function getFeedUpdater(): TwitterV2FeedUpdater
    {
        return GeneralUtility::makeInstance(TwitterV2FeedUpdater::class);
    }
}
