<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\InstagramFactory;
use Pixelant\PxaSocialFeed\Feed\Source\InstagramSource;
use Pixelant\PxaSocialFeed\Feed\Update\InstagramFeedUpdater;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class FacebookFeedFactoryTest
 */
class InstagramFactoryTest extends UnitTestCase
{
    /**
     * @var InstagramFactory
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new InstagramFactory();
        \TYPO3\CMS\Core\Utility\GeneralUtility::setSingletonInstance(\Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository::class, $this->createMock(\Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository::class));
    }

    protected function tearDown(): void
    {
        unset($this->subject);
        \TYPO3\CMS\Core\Utility\GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getFeedSourceReturnInstagramSource()
    {
        self::assertInstanceOf(InstagramSource::class, $this->subject->getFeedSource(new Configuration()));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getFeedUpdaterReturnInstagramUpdater()
    {
        self::assertInstanceOf(InstagramFeedUpdater::class, $this->subject->getFeedUpdater());
    }
}
