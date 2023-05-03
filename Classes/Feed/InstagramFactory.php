<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\Source\InstagramSource;
use Pixelant\PxaSocialFeed\Feed\Update\InstagramFeedUpdater;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class InstagramFactory
 */
class InstagramFactory implements FeedFactoryInterface
{
    /**
     * Feed source allow to fetch feed items
     *
     * @param Configuration $configuration
     * @return InstagramSource
     */
    public function getFeedSource(Configuration $configuration): InstagramSource
    {
        return GeneralUtility::makeInstance(InstagramSource::class, $configuration);
    }

    /**
     * Feed updater. Will create/update feed items
     *
     * @return InstagramFeedUpdater
     */
    public function getFeedUpdater(): InstagramFeedUpdater
    {
        return GeneralUtility::makeInstance(InstagramFeedUpdater::class);
    }
}
