<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Update;

use Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class BaseUpdater
 * @package Pixelant\PxaSocialFeed\Feed\Update
 */
abstract class BaseUpdater implements FeedUpdaterInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var FeedRepository
     */
    protected $feedRepository = null;

    /**
     * BaseUpdater constructor.
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->feedRepository = $this->objectManager->get(FeedRepository::class);
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
