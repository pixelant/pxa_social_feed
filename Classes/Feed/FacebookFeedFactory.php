<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\Source\FacebookSource;
use Pixelant\PxaSocialFeed\Feed\Source\FeedSourceInterface;
use Pixelant\PxaSocialFeed\Feed\Update\FacebookFeedUpdater;
use Pixelant\PxaSocialFeed\Feed\Update\FeedUpdaterInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FacebookFeedFactory
 * @package Pixelant\PxaSocialFeed\Feed
 */
class FacebookFeedFactory implements FeedFactoryInterface
{

    /**
     * Feed source allow to fetch feed items
     *
     * @param Configuration $configuration
     * @return FeedSourceInterface
     */
    public function getFeedSource(Configuration $configuration): FeedSourceInterface
    {
        return GeneralUtility::makeInstance(FacebookSource::class, $configuration);
    }

    /**
     * Feed updater. Will create/update feed items
     *
     * @return FeedUpdaterInterface
     */
    public function getFeedUpdater(): FeedUpdaterInterface
    {
        return GeneralUtility::makeInstance(FacebookFeedUpdater::class);
    }
}
