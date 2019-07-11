<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Update;

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

        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($items,'Debug',16);
        die(__METHOD__);
    }
}
