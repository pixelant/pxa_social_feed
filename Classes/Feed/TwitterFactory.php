<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\Source\TwitterSource;
use Pixelant\PxaSocialFeed\Feed\Update\TwitterFeedUpdater;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TwitterFactory
 * @package Pixelant\PxaSocialFeed\Feed
 */
class TwitterFactory implements FeedFactoryInterface
{

    /**
     * Feed source allow to fetch feed items
     *
     * @param Configuration $configuration
     * @return TwitterSource
     */
    public function getFeedSource(Configuration $configuration): TwitterSource
    {
        return GeneralUtility::makeInstance(TwitterSource::class, $configuration);
    }

    /**
     * Feed updater. Will create/update feed items
     *
     * @return TwitterFeedUpdater
     */
    public function getFeedUpdater(): TwitterFeedUpdater
    {
        return GeneralUtility::makeInstance(TwitterFeedUpdater::class);
    }
}
