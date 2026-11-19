<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed\Update;

use Pixelant\PxaSocialFeed\Domain\Model\Feed;
use Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository;
use Pixelant\PxaSocialFeed\Feed\Update\BaseUpdater;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class BaseUpdaterTest
 */
class BaseUpdaterTest extends UnitTestCase
{
    /**
     * @var BaseUpdater
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $dispatcher = $this->createMock(\Psr\EventDispatcher\EventDispatcherInterface::class);
        $dispatcher->method('dispatch')->willReturnArgument(0);
        GeneralUtility::addInstance(\Psr\EventDispatcher\EventDispatcherInterface::class, $dispatcher);
        $this->subject = $this->getAccessibleMock(BaseUpdater::class, ['update'], [], '', false);
    }

    protected function tearDown(): void
    {
        unset($this->subject);
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function addOrUpdateFeedItemSaveItemInFeedsStorage()
    {
        $feed = new Feed();
        $feedStorage = new ObjectStorage();

        $this->subject->_set('feedRepository', $this->createMock(FeedRepository::class));
        $this->subject->_set('feeds', $feedStorage);

        $this->subject->_call('addOrUpdateFeedItem', $feed);

        self::assertEquals(1, $feedStorage->count());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function addOrUpdateFeedItemCallAddOnNewItem()
    {
        $feed = new Feed();
        $mockedRepository = $this->createMock(FeedRepository::class);
        $mockedRepository
            ->expects($this->once())
            ->method('add')
            ->with($feed);

        $this->subject->_set('feedRepository', $mockedRepository);
        $this->subject->_set('feeds', $this->createMock(ObjectStorage::class));

        $this->subject->_call('addOrUpdateFeedItem', $feed);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function addOrUpdateFeedItemCallUpdateOnExistingItem()
    {
        $feed = new Feed();
        $feed->_setProperty('uid', 1);

        $mockedRepository = $this->createMock(FeedRepository::class);
        $mockedRepository
            ->expects($this->once())
            ->method('update')
            ->with($feed);

        $this->subject->_set('feedRepository', $mockedRepository);
        $this->subject->_set('feeds', $this->createMock(ObjectStorage::class));

        $this->subject->_call('addOrUpdateFeedItem', $feed);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function encodeMessageForSimpleStringReturnSameString()
    {
        $value = 'test string';

        self::assertEquals($value, $this->subject->_call('encodeMessage', $value));
    }
}
