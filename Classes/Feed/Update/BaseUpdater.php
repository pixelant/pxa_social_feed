<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Update;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Model\Feed;
use Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository;
use Pixelant\PxaSocialFeed\SignalSlot\EmitSignalTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * Class BaseUpdater
 */
abstract class BaseUpdater implements FeedUpdaterInterface
{
    use EmitSignalTrait;

    /**
     * @var FeedRepository
     */
    protected $feedRepository;

    /**
     * Keep all processed feed items
     *
     * @var ObjectStorage<Feed>
     */
    protected $feeds;

    /**
     * BaseUpdater constructor.
     */
    public function __construct()
    {
        $this->feedRepository = GeneralUtility::makeInstance(FeedRepository::class);
        $this->feeds = new ObjectStorage();
    }

    /**
     * Persist changes
     */
    public function persist(): void
    {
        GeneralUtility::makeInstance(PersistenceManagerInterface::class)->persistAll();
    }

    /**
     * Clean all outdated records
     *
     * @param Configuration $configuration
     */
    public function cleanUp(Configuration $configuration): void
    {
        if (count($this->feeds) > 0) {
            /** @var Feed $feedToRemove */
            foreach ($this->feedRepository->findNotInStorage($this->feeds, $configuration) as $feedToRemove) {
                // todo: remove in next major version
                /** @deprecated The call to changedFeedItem is deprecated and will be removed in version 4 */
                $this->getSignalSlotDispatcher()->dispatch(__CLASS__, 'changedFeedItem', [$feedToRemove]);

                $this->getSignalSlotDispatcher()->dispatch(__CLASS__, 'removedFeedItem', [$feedToRemove]);
                $this->feedRepository->remove($feedToRemove);
            }
        }
    }

    /**
     * Add or update feed object.
     * Save all processed items
     *
     * @param Feed $feed
     */
    protected function addOrUpdateFeedItem(Feed $feed): void
    {
        // Check if $feed is new or modified and emit change event
        if ($feed->_isDirty() || $feed->_isNew()) {
            $this->getSignalSlotDispatcher()->dispatch(__CLASS__, 'changedFeedItem', [$feed]);
        }

        $this->feeds->attach($feed);
        $this->feedRepository->{$feed->_isNew() ? 'add' : 'update'}($feed);
    }

    /**
     * Use json_encode to get emoji character convert to unicode
     * @TODO is there better way to do this ?
     *
     * @param $message
     * @return string
     */
    protected function encodeMessage(string $message): string
    {
        return substr(json_encode($message), 1, -1);
    }
}
