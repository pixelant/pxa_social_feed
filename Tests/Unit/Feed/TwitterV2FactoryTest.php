<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\Source\TwitterV2Source;
use Pixelant\PxaSocialFeed\Feed\TwitterV2Factory;
use Pixelant\PxaSocialFeed\Feed\Update\TwitterV2FeedUpdater;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class TwitterV2FactoryTest
 */
class TwitterV2FactoryTest extends UnitTestCase
{
    /**
     * @var TwitterV2Factory
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new TwitterV2Factory();
        \TYPO3\CMS\Core\Utility\GeneralUtility::setSingletonInstance(\Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository::class, $this->createMock(\Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository::class));
    }

    protected function tearDown(): void
    {
        unset($this->subject);
        \TYPO3\CMS\Core\Utility\GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getFeedSourceReturnTwitterV2Source()
    {
        self::assertInstanceOf(TwitterV2Source::class, $this->subject->getFeedSource(new Configuration()));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getFeedUpdaterReturnTwitterV2Updater()
    {
        self::assertInstanceOf(TwitterV2FeedUpdater::class, $this->subject->getFeedUpdater());
    }
}
