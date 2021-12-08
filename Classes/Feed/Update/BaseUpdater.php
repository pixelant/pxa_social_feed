<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Update;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Model\Feed;
use Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository;
use Pixelant\PxaSocialFeed\SignalSlot\EmitSignalTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * Class BaseUpdater
 * @package Pixelant\PxaSocialFeed\Feed\Update
 */
abstract class BaseUpdater implements FeedUpdaterInterface
{
    use EmitSignalTrait;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var FeedRepository
     */
    protected $feedRepository = null;

    /**
     * Keep all processed feed items
     *
     * @var ObjectStorage
     */
    protected $feeds = null;

    /**
     * BaseUpdater constructor.
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->feedRepository = $this->objectManager->get(FeedRepository::class);
        $this->feeds = new ObjectStorage();
    }

    /**
     * Persist changes
     */
    public function persist(): void
    {
        $this->objectManager->get(PersistenceManagerInterface::class)->persistAll();
    }

    /**
     * Clean all outdated records
     *
     * @param Configuration $configuration
     */
    public function cleanUp(Configuration $configuration): void
    {
        if (count($this->feeds) > 0) {
            $this->feedRepository->removeNotInStorage($this->feeds, $configuration);
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
