<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\Source\FeedSourceInterface;
use Pixelant\PxaSocialFeed\Feed\Source\YoutubeSource;
use Pixelant\PxaSocialFeed\Feed\Update\FeedUpdaterInterface;
use Pixelant\PxaSocialFeed\Feed\Update\YoutubeFeedUpdater;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class YoutubeFactory
 * @package Pixelant\PxaSocialFeed\Feed
 */
class YoutubeFactory implements FeedFactoryInterface
{

    /**
     * Feed source allow to fetch feed items
     *
     * @param Configuration $configuration
     * @return YoutubeSource
     */
    public function getFeedSource(Configuration $configuration): YoutubeSource
    {
        return GeneralUtility::makeInstance(YoutubeSource::class, $configuration);
    }

    /**
     * Feed updater. Will create/update feed items
     *
     * @return YoutubeFeedUpdater
     */
    public function getFeedUpdater(): YoutubeFeedUpdater
    {
        return GeneralUtility::makeInstance(YoutubeFeedUpdater::class);
    }
}
