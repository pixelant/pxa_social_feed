<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed\Update;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Feed;
use Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository;
use Pixelant\PxaSocialFeed\Feed\Update\BaseUpdater;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
        $reflection = new \ReflectionProperty(GeneralUtility::class, 'singletonInstances');
        $reflection->setAccessible(true);
        $singletonInstances = $reflection->getValue();
        $singletonInstances[ObjectManager::class] = $this->createMock(ObjectManager::class);
        $reflection->setValue(null, $singletonInstances);

        $this->subject = $this->getAccessibleMock(BaseUpdater::class, ['update'], [], '', false);
    }

    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function addOrUpdateFeedItemSaveItemInFeedsStorage()
    {
        $feed = new Feed();
        $feedStorage = new ObjectStorage();

        $this->inject($this->subject, 'feedRepository', $this->createMock(FeedRepository::class));
        $this->inject($this->subject, 'feeds', $feedStorage);

        $this->subject->_call('addOrUpdateFeedItem', $feed);

        self::assertEquals(1, $feedStorage->count());
    }

    /**
     * @test
     */
    public function addOrUpdateFeedItemCallAddOnNewItem()
    {
        $feed = new Feed();
        $mockedRepository = $this->createMock(FeedRepository::class);
        $mockedRepository
            ->expects(self::once())
            ->method('add')
            ->with($feed);

        $this->inject($this->subject, 'feedRepository', $mockedRepository);
        $this->inject($this->subject, 'feeds', $this->createMock(ObjectStorage::class));

        $this->subject->_call('addOrUpdateFeedItem', $feed);
    }

    /**
     * @test
     */
    public function addOrUpdateFeedItemCallUpdateOnExistingItem()
    {
        $feed = new Feed();
        $feed->_setProperty('uid', 1);

        $mockedRepository = $this->createMock(FeedRepository::class);
        $mockedRepository
            ->expects(self::once())
            ->method('update')
            ->with($feed);

        $this->inject($this->subject, 'feedRepository', $mockedRepository);
        $this->inject($this->subject, 'feeds', $this->createMock(ObjectStorage::class));

        $this->subject->_call('addOrUpdateFeedItem', $feed);
    }

    /**
     * @test
     */
    public function encodeMessageForSimpleStringReturnSameString()
    {
        $value = 'test string';

        self::assertEquals($value, $this->subject->_call('encodeMessage', $value));
    }
}
